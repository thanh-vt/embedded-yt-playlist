window.addEventListener('load', (event) => {
    const mainDiv = document.getElementById('eyp_main');
    if (mainDiv) {
        console.log('Found main div', mainDiv);
        setTimeout(() => {
            mainDiv.innerHTML = `<span>Surprised motherfucker</span>`
            mainDiv.style.backgroundColor = 'cyan';
        }, 1500);
    }
    console.log('page fully loaded', eyp_ajax_object);
});