<?php
$bmi = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $heightInCM = isset($_POST['height']) ? floatval($_POST['height']) : 0;
    
    if ($weight > 0 && $heightInCM > 0) {
        $heightInM = $heightInCM / 100;
        $bmi = $weight / ($heightInM * $heightInM);
        $bmi = number_format($bmi, 2);

        if ($bmi < 18.5) {
            $message = "You are underweight.";
            $minHealthyWeight = 18.5 * ($heightInM * $heightInM);
            $weightDifference = number_format($minHealthyWeight - $weight, 2);
            $message .= " You need to gain at least $weightDifference kg to be in the healthy range.";
        } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
            $message = "You are in the healthy weight range.";
        } elseif ($bmi >= 25 && $bmi <= 29.9) {
            $message = "You are overweight.";
            $maxHealthyWeight = 24.9 * ($heightInM * $heightInM);
            $weightDifference = number_format($weight - $maxHealthyWeight, 2);
            $message .= " You need to lose at least $weightDifference kg to be in the healthy range.";
        } else {
            $message = "You are obese.";
            $maxHealthyWeight = 24.9 * ($heightInM * $heightInM);
            $weightDifference = number_format($weight - $maxHealthyWeight, 2);
            $message .= " You need to lose at least $weightDifference kg to be in the healthy range.";
        }
    } else {
        $message = "Please enter valid height and weight values.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 2px 2px 12px rgba(0,0,0,0.1);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>BMI Calculator</h2>
        <form method="post" action="">
            <label for="weight">Weight (kg):</label>
            <input type="number" step="0.1" id="weight" name="weight" required>
            
            <label for="height">Height (cm):</label>
            <input type="number" step="0.1" id="height" name="height" required>
            
            <button type="submit">Calculate BMI</button>
        </form>
        
        <?php if ($bmi): ?>
            <h3>Your BMI: <?php echo $bmi; ?></h3>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
