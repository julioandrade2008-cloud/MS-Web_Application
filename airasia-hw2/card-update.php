<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Card Update</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">

</head>

<body class="update-page">

    <h1>Update Gift Card</h1>

<div class="form-container">
    <form action="card-list.php" method="post">
        <div class="form-group">
            <label for="cardName">Card Name</label>
            <input type="text" id="cardName" name="cardName" value="Amazon" maxlength="100" placeholder="Enter gift card name" required>
        </div>

        <div class="form-group">
            <label for="cardType">Card Type</label>
            <input type="text" name="cardType" id="cardType" value="Retail" maxlength="100" placeholder="Enter card type" required>
        </div>
        
        <div class="form-group">
            <label for="cardValue">Card Value</label>
            <input type="number" name="cardValue" id="cardValue" value="50.00" min="0" step="0.01" placeholder="Enter card value (e.g., 50.00)" required>
        </div>

        <div class="form-group">
            <label for="pointsRequired">Points Required</label>
            <input type="number" name="pointsRequired" id="pointsRequired" value="5000"  min="0" step="1" placeholder="Enter points required" required>
        </div>
        
        <div>
            <button class="form-button" type="submit">Update</button>
        </div>
    </form>

    <a class="back-link" href="card-list.php">Back to Gift Card List</a>

</div>



</body>


</html>