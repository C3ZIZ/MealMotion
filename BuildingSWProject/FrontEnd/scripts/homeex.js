const mealsContainer = document.querySelector(".meals");
const mealDetailsPopup = document.querySelector("#popup");
const mealDetailsContent = document.querySelector(".popup-content");
const closePopupButton = document.querySelector(".popup .close");

let mealsData = [];

const loadMeals = () => {
  fetch("../../BackEnd/fetchHomeExercises.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();
    })
    .then((data) => {
      console.log("Raw server response:", data);
      try {
        const meals = JSON.parse(data);
        if (meals.message) {
          mealsContainer.innerHTML = `<p>${meals.message}</p>`;
          return;
        }
        mealsData = meals;
        displayMeals(meals);
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

const displayMeals = (meals) => {
  mealsContainer.innerHTML = "";

  meals.forEach((meal) => {
    const mealCard = document.createElement("div");
    mealCard.className = "meal-card";
    mealCard.dataset.type = meal.type;

    const mealThumbnail = meal.thumbnail ? `data:image/jpeg;base64,${meal.thumbnail}` : 'path/to/placeholder-image.jpg';

    mealCard.innerHTML = `
      <h4>${meal.name}</h4>
    `;

    mealCard.addEventListener("click", () => {
      showDetails(
        meal.name,
        meal.text.split("\n").join(", "),
        meal.video_link,
        mealThumbnail
      );
    });

    mealsContainer.appendChild(mealCard);
  });
};

const showDetails = (name, text, video_link, mealThumbnail) => {
  mealDetailsPopup.style.display = "flex";

  mealDetailsContent.innerHTML = `
    <span class="close" onclick="closePopup()">×</span>
    <div id="popupImage">
    </div>
    <h2 id="mealTitle">${name}</h2>
    <p><strong>Details:</strong> ${text}</p>
    <h3>Video:</h3>
    <p>${video_link ? `<a href="${video_link}" target="_blank">Watch here</a>` : "No video available"}</p>
  `;
};

const closePopup = () => {
  mealDetailsPopup.style.display = "none";
};

mealDetailsPopup.addEventListener("click", (e) => {
  if (!mealDetailsContent.contains(e.target)) {
    closePopup();
  }
});

document.addEventListener("DOMContentLoaded", loadMeals);
