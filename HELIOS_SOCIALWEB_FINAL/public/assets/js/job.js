// public/assets/js/job.js

const scrollBtn = document.getElementById('scrollTop');
if (scrollBtn) { // Đảm bảo nút tồn tại trước khi thêm event listener
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    });
}