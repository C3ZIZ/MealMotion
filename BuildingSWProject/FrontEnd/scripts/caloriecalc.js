document.getElementById("calorieForm").addEventListener("submit", function(event) {
    event.preventDefault();


    const weight = parseFloat(document.getElementById("weight").value);
    const heightCm = parseFloat(document.getElementById("height").value);
    const heightM = heightCm / 100; // Convert cm to meters
    const age = parseFloat(document.getElementById("age").value);
    const gender = document.getElementById("gender").value;
    const activityLevel = document.getElementById("activityLevel").value;

    if (!weight || !heightCm || !age || !gender || !activityLevel) {
      alert("Please Fill All Fields");
      return;
    }

    // Calculate BMI
    const bmi = weight / (heightM * heightM);

    // Calculate the perfect weight range using BMI method (healthy BMI 18.5 - 24.9)
    const lowerHealthyWeight = 18.5 * (heightM * heightM);
    const upperHealthyWeight = 24.9 * (heightM * heightM);

    // Mifflin-St Jeor formula for Basal Metabolic Rate (BMR)
    let BMR; // Basal Metabolic Rate
    if (gender === "male") {
      BMR = 10 * weight + 6.25 * heightCm - 5 * age + 5; // Male BMR
    } else {
      BMR = 10 * weight + 6.25 * heightCm - 5 * age - 161; // Female BMR
    }


    let activityMultiplier;
    switch (activityLevel) {
      case "sedentary":
        activityMultiplier = 1.2;
        break;
      case "light":
        activityMultiplier = 1.375;
        break;
      case "moderate":
        activityMultiplier = 1.55;
        break;
      case "active":
        activityMultiplier = 1.725;
        break;
      case "extraActive":
        activityMultiplier = 1.9;
        break;
      default:
        activityMultiplier = 1.2;
    }

    const dailyCalories = BMR * activityMultiplier;

    // Suggested calorie deficit for weight loss (e.g., 500 kcal/day for 0.5 kg loss per week)
    const calorieDeficit = 500; // Standard deficit for weight loss

    // Display results
    document.getElementById("caloriesres").textContent = `${dailyCalories.toFixed(0)} kcal`;
    document.getElementById("weight-range").textContent = `${Math.round(lowerHealthyWeight)} - ${Math.round(upperHealthyWeight)} kg`;
    document.getElementById("mass").textContent = bmi.toFixed(2);
    document.getElementById("calorie-deficit").textContent = `${calorieDeficit} kcal/day`;
});
