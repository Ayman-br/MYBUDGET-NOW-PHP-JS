// Mobile Menu Toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('menuIcon');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.innerHTML = '<path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />';
            } else {
                menu.classList.add('hidden');
                icon.innerHTML = '<path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />';
            }
        });

        // Modal Functions
        function openIncomeModal() {
            const modal = document.getElementById('incomeModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        }

        function closeIncomeModal() {
            const modal = document.getElementById('incomeModal');
            modal.classList.add('hidden');
        }

        function openExpenseModal() {
            const modal = document.getElementById('expenseModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        }

        function closeExpenseModal() {
            const modal = document.getElementById('expenseModal');
            modal.classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const incomeModal = document.getElementById('incomeModal');
            const expenseModal = document.getElementById('expenseModal');
            
            if (event.target === incomeModal) {
                closeIncomeModal();
            }
            if (event.target === expenseModal) {
                closeExpenseModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeIncomeModal();
                closeExpenseModal();
            }
        });

        // Auto-focus first input when modal opens
        function focusFirstInput(modalId) {
            const modal = document.getElementById(modalId);
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        }

 