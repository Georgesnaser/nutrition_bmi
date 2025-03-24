<?php
include 'conx.php';
include 'nav.php';
?>

<body>
    

    <main class="container mt-5">
        <section>
            <h2>Your Weekly Meal Plan</h2>
            <div class="text-center mb-4">
                <button id="export-button" class="btn btn-success btn-sm mx-2">
                    <i class="fas fa-file-export"></i> Export to CSV
                </button>
                <button id="save-button" class="btn btn-primary btn-sm mx-2">
                    <i class="fas fa-save"></i> Save to Database
                </button>
            </div>
            <div id="meal-plan" class="row">Loading meal plan...</div>
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const apiKey = "f99adf078c7b4a23a510ef22b8f1e7e8";
            const demoUrl =`https://api.spoonacular.com/mealplanner/generate?timeFrame=week&apiKey=${apiKey}&targetCalories=1800&diet=high-protein&minProtein=100&maxCalories=2000&exclude=fried,deep-fried,pan-fried&tags=baked,roasted,grilled`;

            async function fetchMealPlan() {
                try {
                    const response = await fetch(demoUrl);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    console.log("API Response:", data); // For debugging
                    if (data.week) {
                        displayMealPlan(data.week);
                        document.getElementById('save-button').addEventListener('click', () => saveMealPlanToDatabase(data.week));
                    } else {
                        throw new Error("Invalid response format");
                    }
                } catch (error) {
                    console.error("Error fetching meal plan:", error);
                    document.getElementById("meal-plan").innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            Error loading meal plan: ${error.message}
                        </div>`;
                }
            }

            function displayMealPlan(weekData) {
                const container = document.getElementById("meal-plan");
                container.innerHTML = "";

                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                
                days.forEach(day => {
                    const dayMeals = weekData[day];
                    if (dayMeals && dayMeals.meals) {
                        const dayCard = document.createElement("div");
                        dayCard.className = "card mb-4";
                        dayCard.innerHTML = `
                            <div class="card-header bg-dark text-white">
                                <h3 class="mb-0"><i class="fas fa-utensils mr-2"></i>${day.charAt(0).toUpperCase() + day.slice(1)}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    ${dayMeals.meals.map(meal => `
                                        <div class="col-md-4">
                                            <div class="card h-100">
                                                <img src="https://spoonacular.com/recipeImages/${meal.id}-636x393.jpg" class="card-img-top" alt="${meal.title}">
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title">${meal.title}</h5>
                                                    <p><i class="far fa-clock mr-2"></i>${meal.readyInMinutes} minutes</p>
                                                    <p><i class="fas fa-users mr-2"></i>${meal.servings} servings</p>
                                                    <a href="${meal.sourceUrl}" class="btn btn-primary mt-auto" target="_blank">
                                                        <i class="fas fa-external-link-alt mr-2"></i>View Recipe
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                ${dayMeals.nutrients ? `
                                    <div class="nutrition-summary">
                                        <h4 class="text-center mb-3">Daily Nutrition Summary</h4>
                                        <ul>
                                            <li><i class="fas fa-fire-alt mr-2"></i><strong>Calories:</strong><br>${Math.round(dayMeals.nutrients.calories)} kcal</li>
                                            <li><i class="fas fa-drumstick-bite mr-2"></i><strong>Protein:</strong><br>${Math.round(dayMeals.nutrients.protein)} g</li>
                                            <li><i class="fas fa-cheese mr-2"></i><strong>Fat:</strong><br>${Math.round(dayMeals.nutrients.fat)} g</li>
                                            <li><i class="fas fa-bread-slice mr-2"></i><strong>Carbs:</strong><br>${Math.round(dayMeals.nutrients.carbohydrates)} g</li>
                                        </ul>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        container.appendChild(dayCard);
                    }
                });
            }

            function exportToCSV(weekData) {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                let csvContent = "data:text/csv;charset=utf-8,";

                days.forEach(day => {
                    const dayMeals = weekData[day];
                    if (dayMeals && dayMeals.meals) {
                        csvContent += `${day.charAt(0).toUpperCase() + day.slice(1)}\n`;
                        csvContent += "Title,Ready in Minutes,Servings,Source URL\n";
                        dayMeals.meals.forEach(meal => {
                            csvContent += `${meal.title},${meal.readyInMinutes},${meal.servings},${meal.sourceUrl}\n`;
                        });
                        csvContent += "\n";
                    }
                });

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "Weekly_Meal_Plan.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            async function saveMealPlanToDatabase(weekData) {
                try {
                    const response = await fetch('save_meal_plan.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(weekData)
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const result = await response.json();
                    console.log("Save to DB Response:", result);
                } catch (error) {
                    console.error("Error saving meal plan to database:", error);
                }
            }

            document.getElementById('export-button').addEventListener('click', async () => {
                try {
                    const response = await fetch(demoUrl);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.week) {
                        exportToCSV(data.week);
                    } else {
                        throw new Error("Invalid response format");
                    }
                } catch (error) {
                    console.error("Error exporting meal plan:", error);
                }
            });

            fetchMealPlan();
        });
    </script>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let input = this.value.toLowerCase();
    let cards = document.getElementsByClassName('card');
    
    Array.from(cards).forEach(card => {
        let text = card.textContent.toLowerCase();
        card.parentElement.style.display = text.includes(input) ? '' : 'none';
    });
});
</script>
</body>
</html>