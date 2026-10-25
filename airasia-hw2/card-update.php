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

    <form action="card-list.php" method="post">
        <div>
            <label for="cardName">Card Name</label>
            <input type="text" id="cardName" name="cardName" value="Amazon" maxlength="100" required>
        </div>

        <div>
            <label for="cardType">Card Type</label>
            <input type="text" name="cardType" id="cardType" value="Retail" maxlength="100" required>
        </div>
        
        <div>
            <label for="cardValue">Card Value</label>
            <input type="number" name="cardValue" id="cardValue" value="50.00" min="0" step="0.01" required>
        </div>

        <div>
            <label for="pointsRequired">Points Required</label>
            <input type="number" name="pointsRequired" id="pointsRequired" value="5000"  min="0" step="1" required>
        </div>

       <div>
        <button type="submit">Update</button>
       </div>
    </form>

    <a href="card-list.php">Back</a>



</body>


</html>