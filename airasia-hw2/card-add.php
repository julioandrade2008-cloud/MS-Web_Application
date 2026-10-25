<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add card</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="add-page">
    <h1>Add Gift Card</h1>

    <form action="card-list.php" method="post">
        <div>
            <label for="cardName">Card Name</label>
            <input type="text" id="cardName" name="cardName" maxlength="100" required>
        </div>

        <div>
            <label for="cardType">Card Type</label>
            <input type="text" name="cardType" id="cardType" maxlength="100" required>
        </div>
        
        <div>
            <label for="cardValue">Card Value</label>
            <input type="number" name="cardValue" id="cardValue" min="0" step="0.01" required>
        </div>

        <div>
            <label for="pointsRequired">Points Required</label>
            <input type="number" name="pointsRequired" id="pointsRequired" min="0" step="1" required>
        </div>
       
        <button type="submit">Submit</button>
    </form>
    <a href="card-list.php">Back</a>
</body>

</html>