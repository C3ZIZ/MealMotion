document.addEventListener('DOMContentLoaded', function () {
    // alter path: http://localhost/BuildingSWProject/backend/getHealthFact.php
  fetch('../../BackEnd/getHealthFact.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('healthFact').textContent = data.fact;
    })
    .catch(error => {
      console.error('Error fetching health fact:', error);
      document.getElementById('healthFact').textContent = 'Unable to load health fact.';
    });
});
