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

            const urlParams = new URLSearchParams(window.location.search);
            const isReplacing = urlParams.get('replace') === 'true';
            const replaceDay = urlParams.get('day');
            const replaceMealId = urlParams.get('mealId');
            const newFoodId = urlParams.get('newFoodId');

            function shouldFetchNewMealPlan() {
                const storedMealPlan = localStorage.getItem('weeklyMealPlan');
                const expirationDate = localStorage.getItem('mealPlanExpiration');
                
                if (!storedMealPlan || !expirationDate) {
                    return true;
                }

                return new Date().getTime() > parseInt(expirationDate);
            }

            function setMealPlanWithExpiration(mealPlanData) {
                // Set expiration to 7 days from now
                const expirationDate = new Date().getTime() + (7 * 24 * 60 * 60 * 1000);
                localStorage.setItem('weeklyMealPlan', JSON.stringify(mealPlanData));
                localStorage.setItem('mealPlanExpiration', expirationDate.toString());
            }

            async function replaceMealInPlan(weekData, day, mealId, newFoodId) {
                try {
                    const response = await fetch(`get_saved_meal.php?id=${newFoodId}`);
                    if (!response.ok) throw new Error('Failed to fetch replacement meal');
                    const newMeal = await response.json();

                    // Find and replace the meal
                    if (weekData[day] && weekData[day].meals) {
                        const mealIndex = weekData[day].meals.findIndex(meal => meal.id == mealId);
                        if (mealIndex !== -1) {
                            // Store original nutrients if not already stored
                            if (!weekData[day].originalNutrients) {
                                weekData[day].originalNutrients = {...weekData[day].nutrients};
                            }

                            // Replace the meal
                            weekData[day].meals[mealIndex] = {
                                id: newMeal.id,
                                title: newMeal.name,
                                sourceUrl: newMeal.source,
                                readyInMinutes: 30, // default value
                                servings: 1 // default value
                            };

                            // Update nutrients
                            if (!weekData[day].replacementNutrients) {
                                weekData[day].replacementNutrients = {...weekData[day].nutrients};
                            }
                            weekData[day].replacementNutrients.calories = newMeal.calories;
                            weekData[day].replacementNutrients.protein = newMeal.protein;

                            // Save updated meal plan
                            setMealPlanWithExpiration(weekData);
                            displayMealPlan(weekData);
                        }
                    }
                } catch (error) {
                    console.error('Error replacing meal:', error);
                }
            }

            async function fetchMealPlan() {
                try {
                    let weekData;
                    if (!shouldFetchNewMealPlan()) {
                        weekData = JSON.parse(localStorage.getItem('weeklyMealPlan'));
                    } else {
                        const response = await fetch(demoUrl);
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        const data = await response.json();
                        weekData = data.week;
                        setMealPlanWithExpiration(weekData);
                    }

                    if (isReplacing && replaceDay && replaceMealId && newFoodId) {
                        await replaceMealInPlan(weekData, replaceDay, replaceMealId, newFoodId);
                        // Clear URL parameters
                        window.history.replaceState({}, '', 'meal_plan.php');
                    } else {
                        displayMealPlan(weekData);
                    }
                } catch (error) {
                    console.error("Error:", error);
                }
            }

            // Add a function to force refresh the meal plan
            window.forceRefreshMealPlan = async () => {
                localStorage.removeItem('weeklyMealPlan');
                localStorage.removeItem('mealPlanExpiration');
                await fetchMealPlan();
            }

            function checkRefreshButtonVisibility() {
                const refreshButton = document.querySelector('.btn.btn-info.btn-sm');
                const expirationDate = localStorage.getItem('mealPlanExpiration');
                
                if (!expirationDate) {
                    refreshButton.style.display = 'none';
                    return;
                }

                const timeUntilExpiration = parseInt(expirationDate) - new Date().getTime();
                if (timeUntilExpiration > 0) {
                    refreshButton.style.display = 'none';
                } else {
                    refreshButton.style.display = 'inline-block';
                }
            }

            // Modify the refresh button creation
            const buttonContainer = document.querySelector('.text-center.mb-4');
            const refreshButton = document.createElement('button');
            refreshButton.className = 'btn btn-info btn-sm mx-2';
            refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i> Force Refresh';
            refreshButton.onclick = window.forceRefreshMealPlan;
            buttonContainer.appendChild(refreshButton);
            
            // Initial check of refresh button visibility
            checkRefreshButtonVisibility();

            // Check visibility every hour
            setInterval(checkRefreshButtonVisibility, 3600000);

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
                                                        <a href="view_saved_meals.php?day=${day}&mealId=${meal.id}" class="btn btn-warning btn-sm mb-2">
                                                            <i class="fas fa-exchange-alt mr-2"></i>Replace Meal
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                ${dayMeals.nutrients ? `
                                    <div class="nutrition-summary row mt-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Original Nutrition Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6 mb-3">
                                                            <i class="fas fa-fire-alt text-danger"></i>
                                                            <strong>Calories</strong>
                                                            <div>${Math.round(dayMeals.originalNutrients?.calories || dayMeals.nutrients.calories)} kcal</div>
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <i class="fas fa-drumstick-bite text-success"></i>
                                                            <strong>Protein</strong>
                                                            <div>${Math.round(dayMeals.originalNutrients?.protein || dayMeals.nutrients.protein)} g</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <i class="fas fa-cheese text-warning"></i>
                                                            <strong>Fat</strong>
                                                            <div>${Math.round(dayMeals.originalNutrients?.fat || dayMeals.nutrients.fat)} g</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <i class="fas fa-bread-slice text-primary"></i>
                                                            <strong>Carbs</strong>
                                                            <div>${Math.round(dayMeals.originalNutrients?.carbohydrates || dayMeals.nutrients.carbohydrates)} g</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ${dayMeals.replacementNutrients ? `
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Updated Nutrition Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6 mb-3">
                                                            <i class="fas fa-fire-alt text-danger"></i>
                                                            <strong>Calories</strong>
                                                            <div>${Math.round(dayMeals.replacementNutrients.calories)} kcal</div>
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <i class="fas fa-drumstick-bite text-success"></i>
                                                            <strong>Protein</strong>
                                                            <div>${Math.round(dayMeals.replacementNutrients.protein)} g</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <i class="fas fa-cheese text-warning"></i>
                                                            <strong>Fat</strong>
                                                            <div>${Math.round(dayMeals.replacementNutrients.fat || 0)} g</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <i class="fas fa-bread-slice text-primary"></i>
                                                            <strong>Carbs</strong>
                                                            <div>${Math.round(dayMeals.replacementNutrients.carbohydrates || 0)} g</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
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


</body>
</html>