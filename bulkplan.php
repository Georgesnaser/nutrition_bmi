<?php
include 'conx.php';
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .nutrition-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .nutrition-summary ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .nutrition-summary li {
            text-align: center;
            padding: 10px;
            min-width: 120px;
            margin: 5px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(45deg, #343a40, #495057);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .alert-info {
            background: linear-gradient(45deg, #cfe2ff, #e9ecef);
            border: none;
        }

        .alert-info ul {
            margin-bottom: 0;
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            border: none;
        }

        .btn-success {
            background: linear-gradient(45deg, #198754, #157347);
            border: none;
        }

        .text-success {
            color: #198754 !important;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .nutrition-summary ul {
                flex-direction: column;
            }
            
            .nutrition-summary li {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <main class="container mt-5">
        <section>
            <h2>Your Weekly Bulk Meal Plan</h2>
            <div class="alert alert-info">
                <h5>Daily Targets for Bulking:</h5>
                <ul>
                    <li>Calories: 3000+ kcal</li>
                    <li>Protein: 180+ g</li>
                    <li>Carbs: 350+ g</li>
                    <li>Fat: 80+ g</li>
                </ul>
            </div>
            <div class="text-center mb-4">
                <button id="export-button" class="btn btn-success btn-sm mx-2">
                    <i class="fas fa-file-export"></i> Export to CSV
                </button>
                <button id="save-button" class="btn btn-primary btn-sm mx-2">
                    <i class="fas fa-save"></i> Save to Database
                </button>
            </div>
            <div id="meal-plan" class="row">Loading your bulk meal plan...</div>
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const apiKey = "f99adf078c7b4a23a510ef22b8f1e7e8";
            const demoUrl = `https://api.spoonacular.com/mealplanner/generate?timeFrame=week&apiKey=${apiKey}&targetCalories=3000&minProtein=180&minCarbs=350&minFat=80`;

            async function fetchMealPlan() {
                try {
                    const response = await fetch(demoUrl);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    console.log("API Response:", data);
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
                        dayCard.className = "col-12 mb-4";
                        dayCard.innerHTML = `
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="mb-0"><i class="fas fa-utensils mr-2"></i>${day.charAt(0).toUpperCase() + day.slice(1)}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        ${dayMeals.meals.map(meal => `
                                            <div class="col-md-4 mb-3">
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
                                    <div class="nutrition-summary">
                                        <h4 class="text-center mb-3">Daily Bulk Diet Nutrition</h4>
                                        <ul>
                                            <li class="${dayMeals.nutrients.calories >= 3000 ? 'text-success' : 'text-danger'}">
                                                <i class="fas fa-fire-alt mr-2"></i><strong>Calories:</strong><br>${Math.round(dayMeals.nutrients.calories)} kcal
                                            </li>
                                            <li class="${dayMeals.nutrients.protein >= 180 ? 'text-success' : 'text-danger'}">
                                                <i class="fas fa-drumstick-bite mr-2"></i><strong>Protein:</strong><br>${Math.round(dayMeals.nutrients.protein)} g
                                            </li>
                                            <li class="${dayMeals.nutrients.fat >= 80 ? 'text-success' : 'text-danger'}">
                                                <i class="fas fa-cheese mr-2"></i><strong>Fat:</strong><br>${Math.round(dayMeals.nutrients.fat)} g
                                            </li>
                                            <li class="${dayMeals.nutrients.carbohydrates >= 350 ? 'text-success' : 'text-danger'}">
                                                <i class="fas fa-bread-slice mr-2"></i><strong>Carbs:</strong><br>${Math.round(dayMeals.nutrients.carbohydrates)} g
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.appendChild(dayCard);
                    }
                });
            }

            // ...existing export and save functions...

            fetchMealPlan();
        });
    </script>
</body>
</html>
