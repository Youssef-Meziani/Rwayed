document.addEventListener('DOMContentLoaded', () => {
    const preferenceInputs = document.querySelectorAll('input[type="radio"][name]');

    preferenceInputs.forEach(input => {
        input.addEventListener('change', () => {
            const preferences = {};
            preferences[ input.name ] = input.value;

            fetch('/save-preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(preferences)
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                })
                .catch(error => {
                    console.error('Error saving preferences:', error);
                });
        });
    });

    function setInitialPreferences(preferences) {
        for (const [ key, value ] of Object.entries(preferences)) {
            document.body.setAttribute(`data-${key}`, value);
            const input = document.querySelector(`input[name="${key}"][value="${value}"]`);
            if (input) {
                input.checked = true;
            }
        }
    }

    fetch('/get-preferences', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(preferences => {
            setInitialPreferences(preferences);
        })
        .catch(error => {
            console.error('Error fetching preferences:', error);
        });
});

document.addEventListener('DOMContentLoaded', () => {
    const preferenceInputs = document.querySelectorAll('input[type="radio"][name]');

    preferenceInputs.forEach(input => {
        input.addEventListener('change', () => {
            const preferences = {};
            preferences[ input.name ] = input.value;

            fetch('/save-preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(preferences)
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                })
                .catch(error => {
                    console.error('Error saving preferences:', error);
                });
        });
    });

    // Set initial preferences based on cookies
    preferenceInputs.forEach(input => {
        const value = getCookie(input.name);
        if (value) {
            input.checked = input.value === value;
        }
    });

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
});