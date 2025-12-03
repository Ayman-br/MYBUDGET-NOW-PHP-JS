<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db.php';

// Set page title
$pageTitle = "MyBudget - Personal Finance Manager";

// Get totals from database
$total_income = 0;
$total_expense = 0;
$balance = 0;

$income_result = mysqli_query($conn, "SELECT SUM(amount) as total FROM incomes");
if ($income_result) {
    $income_row = mysqli_fetch_assoc($income_result);
    $total_income = $income_row['total'] ? floatval($income_row['total']) : 0;
}

$expense_result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses");
if ($expense_result) {
    $expense_row = mysqli_fetch_assoc($expense_result);
    $total_expense = $expense_row['total'] ? floatval($expense_row['total']) : 0;
}

$balance = $total_income - $total_expense;

// Get recent transactions
$recent_query = "
    (SELECT id, amount, description, category, date, 'income' as type FROM incomes ORDER BY date DESC LIMIT 5)
    UNION
    (SELECT id, amount, description, category, date, 'expense' as type FROM expenses ORDER BY date DESC LIMIT 5)
    ORDER BY date DESC LIMIT 10
";
$recent_result = mysqli_query($conn, $recent_query);
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

</head>
<body>
    <header>
        <nav class="relative bg-gray-900/50 after:pointer-events-none after:absolute after:inset-x-0 after:bottom-0 after:h-px after:bg-white/10">
  <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
    <div class="relative flex h-16 items-center justify-between">
      <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
        <!-- Mobile menu button-->
        <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
          <span class="absolute -inset-0.5"></span>
          <span class="sr-only">Open main menu</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="flex flex-1 items-center justify-center gap-64 sm:items-stretch sm:justify-start">
        <div class="flex shrink-0 items-center">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDnBs7HoRobPxXDUqqbm4hiTuQDnrgXa1wOgdDxT0wozxCfAmQ9u90rKE&s" alt="Your Company" class="h-12 w-auto" />
        </div>
        <div class="hidden sm:ml-6 sm:block">
          <div class="flex space-x-4">
            <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
            <a href="#" aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white">Dashboard</a>
            <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">home</a>
            <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">incomes</a>
            <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">expences</a>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <el-disclosure id="mobile-menu" hidden class="block sm:hidden">
    <div class="space-y-1 px-2 pt-2 pb-3">
      <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
      <a href="#" aria-current="page" class="block rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white">Dashboard</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Home</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Incomes</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Expences</a>
    </div>
  </el-disclosure>
</nav>
    <div class="relative isolate overflow-hidden bg-gray-900 py-24 sm:py-32">
  <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&crop=focalpoint&fp-y=.8&w=2830&h=1500&q=80&blend=111827&sat=-100&exp=15&blend-mode=multiply" alt="" class="absolute inset-0 -z-10 size-full object-cover object-right md:object-center" />
  <div aria-hidden="true" class="hidden sm:absolute sm:-top-10 sm:right-1/2 sm:-z-10 sm:mr-10 sm:block sm:transform-gpu sm:blur-3xl">
    <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="aspect-1097/845 w-274.25 bg-linear-to-tr from-[#ff4694] to-[#776fff] opacity-20"></div>
  </div>
  <div aria-hidden="true" class="absolute -top-52 left-1/2 -z-10 -translate-x-1/2 transform-gpu blur-3xl sm:-top-112 sm:ml-16 sm:translate-x-0">
    <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="aspect-1097/845 w-274.25 bg-linear-to-tr from-[#ff4694] to-[#776fff] opacity-20"></div>
  </div>
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-2xl lg:mx-0">
      <h2 class="text-5xl font-semibold tracking-tight text-white sm:text-7xl">Welcome to MyBudget</h2>
      <p class="mt-8 text-lg font-medium text-pretty text-gray-300 sm:text-xl/8">Track your income, manage expenses, and achieve your financial goals with our simple yet powerful budgeting tool.</p>
    </div>
        <div class="flex flex-wrap gap-4 mt-8">
            <a href="addIncome.php" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                <i class="fas fa-plus-circle mr-2"></i>Add Income
            </a>
            <a href="addExpences.php" class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600">
                <i class="fas fa-minus-circle mr-2"></i>Add Expense
            </a>
        </div>
  </div>
</div>
    </header>

     <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-8">
            <!-- Total Income Card -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Income</h3>
                    <div class="bg-green-50 p-2 rounded-full">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800 mb-2">$<?php echo number_format($total_income, 2); ?></p>
                <p class="text-sm text-gray-500">All time income</p>
            </div>
            
            <!-- Total Expense Card -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Expenses</h3>
                    <div class="bg-red-50 p-2 rounded-full">
                        <i class="fas fa-shopping-cart text-red-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800 mb-2">$<?php echo number_format($total_expense, 2); ?></p>
                <p class="text-sm text-gray-500">All time expenses</p>
            </div>
            
            <!-- Balance Card -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Current Balance</h3>
                    <div class="bg-blue-50 p-2 rounded-full">
                        <i class="fas fa-wallet text-blue-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?> mb-2">
                    $<?php echo number_format($balance, 2); ?>
                </p>
                <p class="text-sm text-gray-500">Income - Expenses</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="incomes/create.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-md transition text-center">
                <div class="text-indigo-600 mb-2">
                    <i class="fas fa-plus-circle text-2xl"></i>
                </div>
                <h4 class="font-medium">Add Income</h4>
            </a>
            
            <a href="expenses/create.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:shadow-md transition text-center">
                <div class="text-red-600 mb-2">
                    <i class="fas fa-minus-circle text-2xl"></i>
                </div>
                <h4 class="font-medium">Add Expense</h4>
            </a>
            
            <a href="incomes.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:shadow-md transition text-center">
                <div class="text-green-600 mb-2">
                    <i class="fas fa-list text-2xl"></i>
                </div>
                <h4 class="font-medium">View Incomes</h4>
            </a>
            
            <a href="expenses.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-purple-300 hover:shadow-md transition text-center">
                <div class="text-purple-600 mb-2">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
                <h4 class="font-medium">View Expenses</h4>
            </a>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Transactions</h2>
            
            <?php if ($recent_result && mysqli_num_rows($recent_result) > 0): ?>
            <div class="space-y-4">
                <?php while($transaction = mysqli_fetch_assoc($recent_result)): 
                    $is_income = $transaction['type'] === 'income';
                ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4 <?php echo $is_income ? 'bg-green-100' : 'bg-red-100'; ?>">
                            <i class="<?php echo $is_income ? 'fas fa-arrow-down text-green-600' : 'fas fa-arrow-up text-red-600'; ?>"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($transaction['description']); ?></h4>
                            <p class="text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($transaction['date'])); ?> • 
                                <?php echo htmlspecialchars($transaction['category']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold <?php echo $is_income ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $is_income ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                        </p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $is_income ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst($transaction['type']); ?>
                        </span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 mb-4">No transactions yet. Add your first income or expense!</p>
                <div class="flex justify-center space-x-4">
                    <a href="incomes/create.php" class="bg-green-400 btn-primary px-4 py-2 rounded-lg text-white">
                        <i class="fas fa-plus mr-2"></i>Add Income
                    </a>
                    <a href="expenses/create.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white">
                        <i class="fas fa-plus mr-2"></i>Add Expense
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h3 class="text-xl font-bold mb-2">MyBudget</h3>
                <p class="text-gray-300 mb-4">Personal Finance Manager</p>
                <p class="text-gray-400 text-sm">© <?php echo date('Y'); ?> MyBudget. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('menuBtn').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const btn = document.getElementById('menuBtn');
            if (!menu.contains(event.target) && !btn.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>