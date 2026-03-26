<!-- Coupon Modal -->
<div id="couponModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Have a Coupon Code?</h2>
        <p class="text-gray-600 mb-6">
            If you have a coupon code, you can use it to unlock access to all assessments for free.
        </p>

        <form id="couponForm" class="space-y-4">
            @csrf

            <div>
                <label for="couponCode" class="block text-sm font-medium text-gray-700 mb-2">
                    Coupon Code
                </label>
                <input
                    type="text"
                    id="couponCode"
                    name="code"
                    placeholder="Enter coupon code"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    autocomplete="off"
                    required
                >
            </div>

            <div id="couponError" class="hidden p-3 bg-red-100 border border-red-400 text-red-700 rounded"></div>
            <div id="couponSuccess" class="hidden p-3 bg-green-100 border border-green-400 text-green-700 rounded"></div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition"
                >
                    Apply Coupon
                </button>
                <button
                    type="button"
                    onclick="document.getElementById('couponModal').classList.add('hidden')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 rounded-lg transition"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('couponForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const code = document.getElementById('couponCode').value;
    const errorDiv = document.getElementById('couponError');
    const successDiv = document.getElementById('couponSuccess');

    // Clear messages
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');

    try {
        const response = await fetch('{{ route("coupons.apply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ code }),
        });

        const data = await response.json();

        if (data.success) {
            successDiv.textContent = data.message;
            successDiv.classList.remove('hidden');
            
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }, 1500);
        } else {
            errorDiv.textContent = data.message;
            errorDiv.classList.remove('hidden');
        }
    } catch (error) {
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.classList.remove('hidden');
    }
});

function showCouponModal() {
    document.getElementById('couponCode').value = '';
    document.getElementById('couponError').classList.add('hidden');
    document.getElementById('couponSuccess').classList.add('hidden');
    document.getElementById('couponModal').classList.remove('hidden');
}
</script>
