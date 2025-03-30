<?php
include 'conx.php';
include 'nav.php';

// Check for existing valid meal plan
$query = "SELECT week_data FROM meal_plans WHERE valid_until > NOW() ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$existingPlan = mysqli_fetch_assoc($result);
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
            const demoUrl = `https://api.spoonacular.com/mealplanner/generate?timeFrame=week&apiKey=${apiKey}&targetCalories=1800&diet=high-protein&minProtein=100&maxCalories=2000&exclude=fried,deep-fried,pan-fried&tags=baked,roasted,grilled`;

            // Check localStorage first, then PHP data
            const localStoragePlan = localStorage.getItem('weeklyMealPlan');
            const existingPlan = <?php echo $existingPlan ? $existingPlan['week_data'] : 'null'; ?>;

            // Check if we're returning from view_planner_meals.php with a replacement
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('replace') === 'true') {
                const day = urlParams.get('day');
                const mealId = parseInt(urlParams.get('mealId'));
                const newFoodId = urlParams.get('newFoodId');
                if (day && mealId && newFoodId) {
                    replaceMealWithFood(day, mealId, newFoodId);
                }
            }

            async function fetchMealPlan() {
                try {
                    if (localStoragePlan) {
                        displayMealPlan(JSON.parse(localStoragePlan));
                        return;
                    }
                    
                    if (existingPlan) {
                        displayMealPlan(existingPlan);
                        localStorage.setItem('weeklyMealPlan', JSON.stringify(existingPlan));
                        return;
                    }

                    const response = await fetch(demoUrl);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.week) {
                        displayMealPlan(data.week);
                        localStorage.setItem('weeklyMealPlan', JSON.stringify(data.week));
                        saveMealPlanToDatabase(data.week);
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
                                                    <div class="mt-auto">
                                                        <a href="${meal.sourceUrl}" class="btn btn-primary btn-sm mb-2" target="_blank">
                                                            <i class="fas fa-external-link-alt mr-2"></i>View Recipe
                                                        </a>
                                                        <a href="addfood.php?id=${meal.id}&title=${encodeURIComponent(meal.title)}&servings=${meal.servings}" 
                                                            class="btn btn-success btn-sm mb-2">
                                                            <i class="fas fa-plus mr-2"></i>Add to Planner
                                                        </a>
                                                        <button onclick="window.location.href='view_saved_meals.php?day=${day}&mealId=${meal.id}'" class="btn btn-warning btn-sm mb-2">
                                                            <i class="fas fa-exchange-alt mr-2"></i>Replace Meal
                                                        </button>
                                                        <button onclick="deleteFromPlanner(${meal.id})" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash mr-2"></i>Delete
                                                        </button>
                                                    </div>
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
                        body: JSON.stringify({
                            weekData: weekData,
                            validUntil: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString()
                        })
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

            async function addToPlanner(recipeId, title, servings) {
                const apiKey = "f99adf078c7b4a23a510ef22b8f1e7e8";
                const url = `https://api.spoonacular.com/mealplanner/dsky/items?apiKey=${apiKey}`;
                
                const date = new Date();
                const formattedDate = date.getFullYear() + 
                                    '-' + String(date.getMonth() + 1).padStart(2, '0') + 
                                    '-' + String(date.getDate()).padStart(2, '0');

                const mealData = {
                    date: formattedDate,
                    slot: 1,
                    position: 0,
                    type: "RECIPE",
                    value: {
                        id: recipeId,
                        title: title,
                        servings: servings
                    }
                };

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(mealData)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const result = await response.json();
                    alert('Recipe added to your meal planner successfully!');
                } catch (error) {
                    console.error("Error adding to meal planner:", error);
                    alert('Failed to add recipe to meal planner');
                }
            }

            async function deleteFromPlanner(recipeId) {
                const apiKey = "f99adf078c7b4a23a510ef22b8f1e7e8";
                const hash = "4b5v4398573406"; // Your hash value
                const url = `https://api.spoonacular.com/mealplanner/dsky/items/${recipeId}?hash=${hash}&apiKey=${apiKey}`;

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const result = await response.json();
                    alert('Recipe deleted from your meal planner successfully!');
                } catch (error) {
                    console.error("Error deleting from meal planner:", error);
                    alert('Failed to delete recipe from meal planner');
                }
            }

            async function replaceMealWithFood(day, mealId, newFoodId) {
                try {
                    // Fetch saved meal from database instead of API
                    const response = await fetch(`get_saved_meal.php?id=${newFoodId}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const newMeal = await response.json();
                    const weekPlan = JSON.parse(localStorage.getItem('weeklyMealPlan'));
                    
                    weekPlan[day].meals = weekPlan[day].meals.map(meal => 
                        meal.id === mealId ? {
                            id: newMeal.id,
                            title: newMeal.name, // Changed from title to name to match database
                            readyInMinutes: 30, // Default value since it's not in saved meals
                            servings: 1, // Default value since it's not in saved meals
                            sourceUrl: newMeal.source
                        } : meal
                    );
                    
                    localStorage.setItem('weeklyMealPlan', JSON.stringify(weekPlan));
                    displayMealPlan(weekPlan);
                    saveMealPlanToDatabase(weekPlan);
                } catch (error) {
                    console.error("Error replacing meal:", error);
                    alert('Failed to replace meal');
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
</body>
</html>