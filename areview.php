<?php
include 'db_connect.php';
session_start();

$emp_id = $_SESSION['emp_id'] ?? null;

if (!isset($emp_id)) {
    header('location:alogin.php');
    exit();
}

// Function to update JSON file
function updateReviewsJson($conn) {
    $stmt = $conn->query("SELECT * FROM review");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $jsonData = json_encode($reviews, JSON_PRETTY_PRINT);
    file_put_contents('reviews_data.json', $jsonData);
}

// Handle delete action
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM review WHERE Rev_ID = ?");
    $stmt->execute([$delete_id]);
    
    updateReviewsJson($conn);
    header("location:areview.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reviews</title>
    <link rel="stylesheet" href="css/mystyle.css">
    <style>
        .admin_messages {
            width: 100%;
            margin-top: 5rem; 
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
        }

        .admin_box_container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 60%;
            max-width: 800px;
        }

        .admin_box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.05);
            transition: 0.3s ease all;
            color: #333;
        }

        .admin_box:hover {
            transform: translateY(-3px);
        }

        .admin_box p {
            font-size: 1rem;
            margin: 0.4rem 0;
        }

        .admin_box span {
            color: #b22222;
            font-weight: 600;
        }

        .delete-btn {
            margin-top: 1rem;
            padding: 0.5rem 1.2rem;
            background-color: #b22222;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #8b0000;
        }

        .empty {
            font-size: 1.1rem;
            color: gray;
            margin-top: 2rem;
        }

        .controls {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .controls button {
            padding: 0.5rem 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .json-display {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            max-height: 300px;
            overflow-y: auto;
            color: #333;
        }
    </style>
</head>
<body>

<?php include 'aheader.php'; ?>

<section class="admin_messages">
    <div class="controls">
        <button id="loadFromDb">Load from Database</button>
        <button id="loadFromJson">Load from JSON File</button>
        <button id="validateJson">Validate JSON Schema</button>
        <button id="exportToJson">Export to JSON</button>
    </div>

    <div class="admin_box_container" id="review_container">
        <!-- Reviews will be loaded here via AJAX -->
    </div>

    <div id="jsonDisplay" class="json-display" style="display: none;">
        <h3>JSON Data:</h3>
        <pre id="jsonContent"></pre>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ajv/6.12.6/ajv.min.js"></script>
<script>
$(document).ready(function() {
    // Initial load from database
    loadReviewsFromDb();

    // Event handlers for buttons
    $('#loadFromDb').click(function() {
        loadReviewsFromDb();
    });

    $('#loadFromJson').click(function() {
        loadReviewsFromJson();
    });

    $('#validateJson').click(function() {
        validateJsonSchema();
    });

    $('#exportToJson').click(function() {
        exportToJson();
    });

    // Function to load reviews from database via AJAX
    function loadReviewsFromDb() {
        $.ajax({
            url: 'get_reviews.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    displayReviews(data.reviews);
                    $('#jsonDisplay').hide();
                } else {
                    $('#review_container').html('<p class="empty">' + data.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#review_container').html('<p class="empty">Failed to load reviews from database!</p>');
                console.error("Error loading reviews:", error);
            }
        });
    }

    // Function to load reviews from JSON file via AJAX
    function loadReviewsFromJson() {
        $.ajax({
            url: 'reviews_data.json',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                displayReviews(data);
                // Show the JSON content
                $('#jsonContent').text(JSON.stringify(data, null, 2));
                $('#jsonDisplay').show();
            },
            error: function(xhr, status, error) {
                $('#review_container').html('<p class="empty">Failed to load reviews from JSON file!</p>');
                console.error("Error loading JSON file:", error);
            }
        });
    }

    // Function to validate JSON against schema
    function validateJsonSchema() {
        $.when(
            $.getJSON('reviews_data.json'),
            $.getJSON('schemas/JSONSchema_Review.json')
        ).then(function(data, schema) {
            const ajv = new Ajv();
            const validate = ajv.compile(schema[0]);
            const valid = validate(data[0]);

            if (valid) {
                alert('JSON data is valid according to the schema!');
            } else {
                let errors = "JSON validation errors:\n";
                validate.errors.forEach(err => {
                    errors += `- ${err.instancePath} ${err.message}\n`;
                });
                alert(errors);
            }
        }).fail(function() {
            alert('Error loading JSON or schema files!');
        });
    }

    // Function to export current reviews to JSON
    function exportToJson() {
        $.ajax({
            url: 'export_reviews.php',
            type: 'POST',
            success: function(response) {
                alert('Reviews exported to JSON file successfully!');
                loadReviewsFromJson(); // Refresh with the new JSON data
            },
            error: function(xhr, status, error) {
                alert('Failed to export reviews to JSON!');
                console.error("Export error:", error);
            }
        });
    }

    // Function to display reviews in the container
    function displayReviews(reviews) {
        const $container = $('#review_container');
        $container.empty();

        if (Array.isArray(reviews) && reviews.length > 0) {
            reviews.forEach(function(review) {
                $container.append(`
                    <div class="admin_box">
                        <p>Name: <span>${review.Cust_name}</span></p>
                        <p>Rating: <span>${review.Rating}</span></p>
                        <p>Product Title: <span>${review.prod_title}</span></p>
                        <p>Review: <span>${review.Comment}</span></p>
                        <p>Date: <span>${review.Rev_Date}</span></p>
                        <a href="areview.php?delete=${review.Rev_ID}" onclick="return confirm('Are you sure you want to delete this review?');" class="delete-btn">Delete</a>
                    </div>
                `);
            });
        } else {
            $container.html('<p class="empty">No reviews found!</p>');
        }
    }
});
</script>

</body>
</html>