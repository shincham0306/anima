const target = document.getElementById('target');
target.addEventListener('change', function (e) {
    const file = e.target.files[0]
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById("myImage")
        img.src = e.target.result;
    }
    reader.readAsDataURL(file);
}, false);
