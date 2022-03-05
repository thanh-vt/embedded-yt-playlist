window.addEventListener('load', (_) => {
    const mainDiv = document.getElementById('eyp_main');
    if (mainDiv) {
        console.log('Found main div', mainDiv);
        setTimeout(() => {
            mainDiv.innerHTML = `<span>Surprised motherfucker</span>`
            mainDiv.style.backgroundColor = 'cyan';
        }, 1500);
    }
});
