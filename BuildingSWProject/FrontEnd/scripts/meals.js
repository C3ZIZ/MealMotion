// DOM Elements
const mealsContainer = document.querySelector(".meals");
const mealDetailsPopup = document.querySelector("#popup");
const mealDetailsContent = document.querySelector(".popup-content");
const closePopupButton = document.querySelector(".popup .close");

let mealsData = [];

// Load meals from the server
const loadMeals = () => {
  fetch("../../BackEnd/fetchMeals.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();  // Get the response as text (not JSON yet)
    })
    .then((data) => {
      console.log("Raw server response:", data);  // Log the raw response
      try {
        const meals = JSON.parse(data);  // Try parsing the text as JSON
        if (meals.message) { // Handle "No meals found" message
          mealsContainer.innerHTML = `<p>${meals.message}</p>`;
          return;
        }
        mealsData = meals;
        filterMeals(); // Apply the filter on initial load
      } catch (error) {
        console.error("Error parsing JSON:", error);
        mealsContainer.innerHTML = `<p>Error parsing meals data.</p>`;
      }
    })
    .catch((error) => {
      console.error("Error fetching meals:", error);
      mealsContainer.innerHTML = `<p>Error loading meals: ${error.message}</p>`;
    });
};

// Display meals function
const displayMeals = (meals) => {
  mealsContainer.innerHTML = "";  // Clear previous content

  meals.forEach((meal) => {
    const mealCard = document.createElement("div");
    mealCard.className = "meal-card";
    mealCard.dataset.type = meal.type;
    mealCard.dataset.protein = meal.protein;
    mealCard.dataset.calories = meal.calories;

    // Check if thumbnail is null and provide a fallback image if necessary
    const mealThumbnail = meal.thumbnail ? `data:image/jpeg;base64,${meal.thumbnail}` : 'path/to/placeholder-image.jpg';

    mealCard.innerHTML = `
      <img src="${mealThumbnail}" alt="${meal.name}">
      <h4>${meal.name}</h4>
      <p>Protein: ${meal.protein}g | Calories: ${meal.calories}</p>
    `;

    mealCard.addEventListener("click", () => {
      showDetails(
        meal.name,
        `${meal.calories} calories`,
        meal.protein,
        meal.carbs,
        meal.fat,
        meal.type,
        meal.ingredients.split(",").join(", "),  // Format ingredients as comma-separated
        meal.directions,
        mealThumbnail,  // Use base64 for the image or the fallback
        meal.video_link // Assuming the meal data contains a video link
      );
    });

    mealsContainer.appendChild(mealCard);
  });
};

// Display the popup with detailed meal info
const showDetails = (name, calories, protein, carbs, fat, mealType, ingredients, directions, imageSrc, videoLink) => {
  mealDetailsPopup.style.display = "flex";
  mealDetailsContent.innerHTML = `
    <span class="close" onclick="closePopup()">×</span>
    <div id="popupImage">
      <img id="popupImageSrc" src="${imageSrc}" alt="${name}">
    </div>
    <h2 id="mealTitle">${name}</h2>
    <p><strong>Calories:</strong> ${calories}</p>
    <p><strong>Protein:</strong> ${protein}g</p>
    <p><strong>Carbs:</strong> ${carbs}g</p>
    <p><strong>Fat:</strong> ${fat}g</p>
    <p><strong>Type:</strong> ${mealType}</p>
    <h3>Ingredients:</h3>
    <p>${ingredients}</p>
    <h3>Directions:</h3>
    <p>${directions}</p>
    <h3>Video:</h3>
    <p>${videoLink ? `<a href="${videoLink}" target="_blank">Watch here</a>` : "No video available"}</p>
  `;
};

// Close the popup when the close button is clicked
const closePopup = () => {
  mealDetailsPopup.style.display = "none";
};

// Close the popup when clicking outside the content
mealDetailsPopup.addEventListener("click", (e) => {
  if (!mealDetailsContent.contains(e.target)) {
    closePopup();
  }
});

// Sorting function
const sortMeals = (order) => {
  const sortOption = document.getElementById("sortOption").value; // The selected criterion
  const selectedTypes = Array.from(document.querySelectorAll(".meal-type-checkbox:checked")).map(checkbox => checkbox.value);

  // Filter meals based on the selected meal types
  let filteredMeals = mealsData.filter((meal) => {
    return selectedTypes.length === 0 || selectedTypes.includes(meal.type);
  });

  // Sort the filtered meals
  filteredMeals.sort((a, b) => {
    const aValue = parseInt(a[sortOption]);
    const bValue = parseInt(b[sortOption]);
    return order === "highest" 
      ? bValue - aValue  // Sort in descending order (highest)
      : aValue - bValue;  // Sort in ascending order (lowest)
  });

  displayMeals(filteredMeals); // Re-display sorted meals
};

// Filtering function
const filterMeals = () => {
  const selectedTypes = Array.from(document.querySelectorAll(".meal-type-checkbox:checked")).map(checkbox => checkbox.value);

  // Filter meals based on the selected meal types
  let filteredMeals = mealsData.filter((meal) => {
    return selectedTypes.length === 0 || selectedTypes.includes(meal.type);
  });

  displayMeals(filteredMeals); // Re-display filtered meals
};

// Load meals on page load
document.addEventListener("DOMContentLoaded", loadMeals);

// Sorting buttons
document.getElementById("sortHighest").addEventListener("click", () => {
  sortMeals("highest");
});

document.getElementById("sortLowest").addEventListener("click", () => {
  sortMeals("lowest");
});

// Event listeners for meal type filters
document.querySelectorAll(".meal-type-checkbox").forEach((checkbox) => {
  checkbox.addEventListener("change", filterMeals);
});
