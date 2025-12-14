<?php
require_once 'includes/db.php';

if(isset($_POST['delete_expense'])){

    $expence_id = $_POST['expense_id'];

    if(!empty($expence_id)){
        $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
        $stmt->bind_param("i", $expence_id);
        $stmt->execute();
        $stmt->close();

    }

    header("Location: expenses.php");
    exit();
}

if(isset($_POST['update_expense'])){

    $expence_id = $_POST['expense_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    if(!empty($expence_id) && !empty($amount) && !empty($description) && !empty($date)){
        $stmt = $conn->prepare("
        UPDATE expenses SET amount = ?, description = ?, category = ?, date = ?
        WHERE id = ?
        ");

        $stmt->bind_param(
            "dsssi",
            $amount,
            $description,
            $category,
            $date,
            $expence_id
        );
        
        $stmt->execute();
        $stmt->close();

    }

    header("Location: expences.php");
    exit();
}

if(isset($_POST['add_expense'])){
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    if(!empty($amount) && !empty($description) && !empty($date)){
        $stmt = $conn->prepare("INSERT INTO expenses (amount, description, category, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("dsss", $amount, $description, $category, $date);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: expences.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my budget now</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Modal animations */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 200ms ease-out, transform 200ms ease-out;
        }
        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 200ms ease-in, transform 200ms ease-in;
        }
        
        /* Overlay */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        /* Modal display */
        .fixed.inset-0 {
            display: none;
        }
        
        .fixed.inset-0 .modal-overlay {
            position: fixed;
            inset: 0;
        }
        
        .fixed.inset-0 > div:last-child {
            position: relative;
            z-index: 60;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header>
        <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-600 p-2 rounded-lg">
                        <i class="fas fa-wallet text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">MyBudget</h1>
                        <p class="text-xs text-gray-500">Personal Finance Manager</p>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-indigo-600 font-semibold">Home</a>
                    <a href="incomes.php" class="text-gray-600 hover:text-indigo-600">Incomes</a>
                    <a href="expenses.php" class="text-gray-600 hover:text-indigo-600">Expenses</a>
                    <a href="dashbourd.php" class="text-gray-600 hover:text-indigo-600">Dashboard</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="menuBtn" class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden bg-white border-t hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="index.php" class="block px-3 py-2 rounded-md bg-indigo-50 text-indigo-600">Home</a>
                    <a href="incomes.php" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">Incomes</a>
                    <a href="expenses.php" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">Expenses</a>
                    <a href="dashbourd.php" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Expense Management</h1>
        
        <?php
        $expenses = mysqli_query($conn, "SELECT * FROM expenses ORDER BY date DESC");
        if (mysqli_num_rows($expenses) > 0):
        ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Amount</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($expenses)): ?>
                    <tr class="border-t">
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['category']); ?></td>
                        <td class="px-6 py-4"><?php echo $row['date']; ?></td>
                        <td class="px-6 py-4 text-red-600 font-bold">
                            -$<?php echo number_format($row['amount'], 2); ?>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openEditExpenseModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['description']); ?>', '<?php echo $row['category']; ?>', <?php echo $row['amount']; ?>, '<?php echo $row['date']; ?>')" 
                                    class="text-blue-600 hover:text-blue-800 mr-4">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="openDeleteExpenseModal(<?php echo $row['id']; ?>)" 
                                    class="text-red-600 hover:text-red-800">
                                <i class="fa-sharp fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No expense records found.</p>
        </div>
        <?php endif; ?>
        
        <div class="mt-6 flex justify-center">
            <button onclick="openExpenseModal()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white font-medium transition cursor-pointer">
                <i class="fas fa-plus mr-2"></i>Add Expense
             </button>
        </div>
        
        <div class="mt-6">
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
    
     <!-- Expense Modal -->
    <div id="expenseModal" class="fixed inset-0 z-50 hidden">
        <div class="modal-overlay absolute inset-0"></div>
        
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-md">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-minus-circle mr-2"></i>Add New Expense
                        </h3>
                        <button onclick="closeExpenseModal()" class="text-white hover:text-gray-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Form -->
                <form method="POST" class="p-6">
                    <input type="hidden" name="add_expense" value="1">
                    
                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount ($) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" 
                                   name="amount" 
                                   step="0.01"
                                   min="0"
                                   required
                                   class="pl-8 pr-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="description" 
                               required
                               class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="Groceries, Rent, Gas...">
                    </div>
                    
                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <select name="category" 
                                class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="Food">Food</option>
                            <option value="Transport">Transport</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Bills">Bills</option>
                            <option value="Housing">Housing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <!-- Date -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date" 
                               value="<?php echo date('Y-m-d'); ?>"
                               required
                               class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" 
                                onclick="closeExpenseModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg font-medium hover:from-red-600 hover:to-pink-700 transition">
                            <i class="fas fa-save mr-2"></i>Save Expense
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
    
    <!-- Delete Expense Modal -->
    <div id="deleteExpenseModal" class="fixed inset-0 z-50 hidden">
        <div class="modal-overlay absolute inset-0"></div>
        
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Delete</h3>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this expense?</p>
                <form method="POST">
                    <input type="hidden" id="delete_expense_id" name="expense_id">
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeDeleteExpenseModal()" 
                                class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" name="delete_expense"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Expense Modal -->
    <div id="editExpenseModal" class="fixed inset-0 z-50 hidden">
        <div class="modal-overlay absolute inset-0"></div>
        
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-md">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-pen mr-2"></i>Edit Expense
                    </h3>
                    <button onclick="closeEditExpenseModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" id="edit_expense_id" name="expense_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" min="0" required
                               id="edit_expense_amount" name="amount"
                               class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" required
                               id="edit_expense_description" name="description"
                               class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="Groceries, Rent, Utilities...">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="edit_expense_category" name="category"
                                class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="Food">Food</option>
                            <option value="Rent">Rent</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Transport">Transport</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" required
                               id="edit_expense_date" name="date"
                               class="px-4 py-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditExpenseModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" name="update_expense"
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg hover:from-red-600 hover:to-rose-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Expense Modal Functions
    function openExpenseModal() {
        document.getElementById('expenseModal').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
    
    function closeExpenseModal() {
        document.getElementById('expenseModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }

    // Delete Expense Modal Functions
    function openDeleteExpenseModal(expenseId) {
        document.getElementById('delete_expense_id').value = expenseId;
        document.getElementById('deleteExpenseModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteExpenseModal() {
        document.getElementById('deleteExpenseModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Edit Expense Modal Functions
    function openEditExpenseModal(expenseId, description, category, amount, date) {
        document.getElementById('edit_expense_id').value = expenseId;
        document.getElementById('edit_expense_description').value = description;
        document.getElementById('edit_expense_category').value = category;
        document.getElementById('edit_expense_amount').value = amount;
        document.getElementById('edit_expense_date').value = date;
        
        document.getElementById('editExpenseModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeEditExpenseModal() {
        document.getElementById('editExpenseModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeExpenseModal();
            closeDeleteExpenseModal();
            closeEditExpenseModal();
        }
    });

    // Mobile menu toggle
    document.getElementById('menuBtn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });

    // Close modal when clicking on overlay
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers to all overlays
        const overlays = document.querySelectorAll('.modal-overlay');
        overlays.forEach(overlay => {
            overlay.addEventListener('click', function() {
                // Find which modal this overlay belongs to and close it
                const modal = this.closest('.fixed.inset-0');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });
    });
    </script>
</body>
</html>