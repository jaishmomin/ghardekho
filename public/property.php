<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    die('Property ID missing');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Property Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto p-6 bg-white mt-10 rounded shadow">
    <div id="property-details">
        <p class="text-gray-500">Loading property details...</p>
    </div>

    <div class="mt-6">
        <a href="/" class="text-blue-600 underline">← Back to Home</a>
    </div>
</div>

<script>
async function loadProperty() {
    const res = await fetch(`/api/properties.php?id=<?= $id ?>`);
    const data = await res.json();

    if (!data.success) {
        document.getElementById('property-details').innerHTML =
            '<p class="text-red-600">Property not found</p>';
        return;
    }

    const p = data.data;

    document.getElementById('property-details').innerHTML = `
        <h1 class="text-3xl font-bold mb-2">${p.title}</h1>
        <p class="text-gray-600 mb-4">${p.city || ''}</p>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><strong>Type:</strong> ${p.type}</div>
            <div><strong>Beds:</strong> ${p.beds}</div>
            <div><strong>Baths:</strong> ${p.baths}</div>
            <div><strong>Area:</strong> ${p.sqft} sqft</div>
        </div>

        <p class="text-2xl text-green-600 font-bold mb-6">
            ₹ ${Number(p.price).toLocaleString()}
        </p>

        <div class="mt-8 border-t pt-6">
        <h2 class="text-xl font-bold mb-4">Make an Inquiry</h2>

        <form id="inquiry-form" class="space-y-4">
            <input type="hidden" id="property_id" value="<?= $id ?>">

            <input type="text" id="inq-name" placeholder="Your Name"
                class="w-full border px-3 py-2 rounded" required>

            <input type="email" id="inq-email" placeholder="Your Email"
                class="w-full border px-3 py-2 rounded" required>

            <input type="text" id="inq-phone" placeholder="Phone (optional)"
                class="w-full border px-3 py-2 rounded">

            <textarea id="inq-message" placeholder="Message"
                    class="w-full border px-3 py-2 rounded"></textarea>

            <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded">
                Submit Inquiry
            </button>

            <p id="inq-msg" class="text-sm mt-2"></p>
        </form>
    </div>
        <div class="mt-10 border-t pt-6">
        <h2 class="text-xl font-bold mb-4">Schedule a Visit</h2>

        <form id="visit-form" class="space-y-4">
            <input type="hidden" id="visit_property_id" value="<?= $id ?>">

            <input type="text" id="visit-name" placeholder="Your Name"
                class="w-full border px-3 py-2 rounded" required>

            <input type="email" id="visit-email" placeholder="Your Email"
                class="w-full border px-3 py-2 rounded" required>

            <input type="text" id="visit-phone" placeholder="Phone (optional)"
                class="w-full border px-3 py-2 rounded">

            <select id="visit-type" class="w-full border px-3 py-2 rounded" required>
                <option value="">Select Visit Type</option>
                <option value="virtual">Virtual Visit</option>
                <option value="physical">Physical Visit</option>
            </select>

            <input type="datetime-local" id="visit-date"
                class="w-full border px-3 py-2 rounded" required>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded">
                Schedule Visit
            </button>

            <p id="visit-msg" class="text-sm mt-2"></p>
        </form>
    </div>
    `;
attachInquiryHandler();
attachVisitHandler();
}
loadProperty();

function attachInquiryHandler() {
    const form = document.getElementById('inquiry-form');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            property_id: document.getElementById('property_id').value,
            name: document.getElementById('inq-name').value,
            email: document.getElementById('inq-email').value,
            phone: document.getElementById('inq-phone').value,
            message: document.getElementById('inq-message').value
        };

        const res = await fetch('/api/inquiries.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        const msg = document.getElementById('inq-msg');

        if (data.success) {
            msg.textContent = data.message;
            msg.className = 'text-green-600';
            form.reset();
        } else {
            msg.textContent = data.message;
            msg.className = 'text-red-600';
        }
    });
}

function attachVisitHandler() {
    const form = document.getElementById('visit-form');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            property_id: document.getElementById('visit_property_id').value,
            name: document.getElementById('visit-name').value,
            email: document.getElementById('visit-email').value,
            phone: document.getElementById('visit-phone').value,
            visit_type: document.getElementById('visit-type').value,
            visit_date: document.getElementById('visit-date').value
        };

        const res = await fetch('/api/visits.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        const msg = document.getElementById('visit-msg');

        if (data.success) {
            msg.textContent = data.message;
            msg.className = 'text-green-600';
            form.reset();
        } else {
            msg.textContent = data.message;
            msg.className = 'text-red-600';
        }
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    attachInquiryHandler();
    attachVisitHandler();
});
</script>
</body>
</html>