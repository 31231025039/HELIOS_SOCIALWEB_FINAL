// public/assets/js/network.js

// Data (Lý tưởng là dữ liệu này nên được truyền từ server-side PHP)
const mayKnowUsers = [
  { name: "Phúc Nguyễn",  bio: "Sinh viên Thương mại điện tử - UEH",  sub: "12 kết nối chung",  img: "u1",  banner: "#a0b4b7" },
  { name: "Quỳnh Dao",    bio: "Business Analyst Intern @TechCo",      sub: "5 kết nối chung",   img: "u2",  banner: "bg-secondary-subtle" },
  { name: "Phương Uyên",  bio: "Sinh viên Hệ thống thông tin",         sub: "20 kết nối chung",  img: "u3",  banner: "bg-info-subtle" },
  { name: "Minh Tuấn",    bio: "UI/UX Designer Freelance",             sub: "8 kết nối chung",   img: "u4",  banner: "bg-warning-subtle" },
  { name: "Hoàng Nam",    bio: "Web Developer | PHP & Laravel",         sub: "15 kết nối chung",  img: "u5",  banner: "bg-success-subtle" },
  { name: "Lan Trinh",    bio: "Kế toán quản trị @Vinamilk",           sub: "3 kết nối chung",   img: "u6",  banner: "bg-danger-subtle" },
  { name: "Đức Việt",    bio: "Data Analyst tại Shopee VN",            sub: "32 kết nối chung",  img: "u7",  banner: "bg-primary-subtle" },
  { name: "Khánh An",    bio: "Sinh viên Logistics - UEH",             sub: "9 kết nối chung",   img: "u8",  banner: "bg-dark-subtle" },
  { name: "Minh Hạnh",   bio: "Content Creator | Social Media Manager",sub: "7 kết nối chung",   img: "u9",  banner: "linear-gradient(45deg,#6a11cb,#2575fc)" },
  { name: "Quốc Bảo",    bio: "Sinh viên Marketing - K49 UEH",        sub: "14 kết nối chung",  img: "u10", banner: "bg-success-subtle" },
  { name: "Thanh Thảo",  bio: "HR Executive @Startup Hub",             sub: "2 kết nối chung",   img: "u11", banner: "#ff9a9e" },
  { name: "Hoàng Long",  bio: "Fullstack Developer | React & Node.js", sub: "25 kết nối chung",  img: "u12", banner: "bg-info-subtle" },
];

const popularUsers = [
  { name: "Phùng Hải Long", bio: "CMO | AI Trainer",              sub: "22k người theo dõi", img: "p1",  banner: "#333" },
  { name: "Hồng Nghiêm",   bio: "HRBP | Top Creator",            sub: "28k người theo dõi", img: "p2",  banner: "bg-secondary" },
  { name: "Linh Nguyễn",   bio: "Talent Manager @Tech",           sub: "41k người theo dõi", img: "p3",  banner: "bg-dark",    verified: true },
  { name: "Hao Tran",      bio: "CEO at Vietcetera",              sub: "97k người theo dõi", img: "p4",  banner: "bg-info" },
  { name: "Uyên Thảo",     bio: "Innovation Lady",                sub: "43k người theo dõi", img: "p5",  banner: "bg-primary-subtle" },
  { name: "Phan Vinh",     bio: "Product Manager",                sub: "15k người theo dõi", img: "p6",  banner: "bg-success-subtle" },
  { name: "Bảo Ngọc",      bio: "Business English Graduate",      sub: "5k người theo dõi",  img: "p7",  banner: "bg-warning-subtle" },
  { name: "Đức Hiển",     bio: "Sinh viên Năm cuối UEH",         sub: "12k người theo dõi", img: "p8",  banner: "bg-danger-subtle" },
  { name: "Xuân Phúc",    bio: "Senior Account Executive",        sub: "34k người theo dõi", img: "p9",  banner: "bg-info-subtle" },
  { name: "Thu Hà",        bio: "Career Coach @Helios",           sub: "19k người theo dõi", img: "p10", banner: "bg-light" },
  { name: "Quang Thắng",  bio: "Fullstack Developer",             sub: "8k người theo dõi",  img: "p11", banner: "bg-primary" },
  { name: "Minh Yến",     bio: "Marketing Specialist",            sub: "11k người theo dõi", img: "p12", banner: "bg-secondary-subtle" },
];

const suggestedUsers = [
  { name: "Jane Doe",      bio: "Product Manager tại Tech Solutions", sub: "Dựa trên hoạt động của bạn", img: "s1",  banner: "bg-secondary-subtle" },
  { name: "John Smith",   bio: "Software Engineer @GlobalApp",        sub: "Có 15 kết nối chung",         img: "s2",  banner: "bg-info-subtle" },
  { name: "Alice Martins",bio: "UX Researcher @CreativeLab",          sub: "Cùng trường Đại học",          img: "s3",  banner: "bg-warning-subtle" },
  { name: "Robert King",  bio: "Data Analyst Intern",                 sub: "Dựa trên kỹ năng của bạn",    img: "s4",  banner: "bg-success-subtle" },
  { name: "Maria Tran",   bio: "Digital Marketing @UEH",              sub: "12 kết nối chung",             img: "s5",  banner: "bg-light" },
  { name: "David Vu",     bio: "Backend Developer",                   sub: "Bạn có thể biết họ",           img: "s6",  banner: "bg-dark-subtle" },
  { name: "Sophia Chen",  bio: "Business Consultant",                 sub: "Có 8 kết nối chung",           img: "s7",  banner: "bg-primary-subtle" },
  { name: "Tom Le",       bio: "Graphic Designer",                    sub: "Dựa trên lượt xem hồ sơ",     img: "s8",  banner: "bg-danger-subtle" },
  { name: "Kevin Luu",    bio: "AI Research Assistant",               sub: "Có 20 kết nối chung",          img: "s9",  banner: "bg-info-subtle" },
  { name: "Emily Le",     bio: "HR Manager tại VinGroup",             sub: "Bạn cùng lớp cũ",             img: "s10", banner: "bg-warning-subtle" },
  { name: "Daniel Phan",  bio: "E-commerce Founder",                  sub: "Dựa trên sở thích",            img: "s11", banner: "bg-success-subtle" },
  { name: "Ngân Mai",     bio: "Sinh viên Logistics K48",             sub: "Có 5 kết nối chung",           img: "s12", banner: "bg-secondary-subtle" },
];

// Helper functions
function initials(name) {
  return name.split(' ').map(w => w[0]).slice(-2).join('').toUpperCase();
}

function bannerStyle(banner) {
  if (banner.startsWith('bg-') ) return `class="member-banner ${banner}"`;
  return `class="member-banner" style="background:${banner};"`;
}

// Renderer functions
// Cần một cách để truyền baseUrl từ PHP vào JS
// Một cách là định nghĩa một biến JS toàn cục trong layout chính
// Ví dụ: <script>const BASE_URL = '<?php echo $baseUrl; ?>';</script>
// và sử dụng BASE_URL trong các hàm này
function renderConnectCard(user, colClass = 'col-6 col-md-4 col-xl-4', baseUrl = window.BASE_URL) {
  const ini = initials(user.name);
  const verifiedBadge = user.verified
    ? ' <i class="bi bi-patch-check-fill text-primary"></i>' : '';
  return `
    <div class="${colClass}">
      <div class="member-card border rounded-3 overflow-hidden position-relative h-100 bg-white shadow-sm">
        <button class="btn-close-member"><i class="bi bi-x-lg"></i></button>
        <div ${bannerStyle(user.banner)}></div>
        <div class="text-center px-2 pb-3">
          <div class="member-avatar">
            <img src="${baseUrl}public/assets/images/${user.img}.jpg" alt="${user.name}"
                 class="w-100 h-100 object-fit-cover"
                 onerror="this.style.display='none';this.parentElement.innerHTML='${ini}';">
          </div>
          <h6 class="fw-bold mb-0 text-truncate">${user.name}${verifiedBadge}</h6>
          <p class="text-muted extra-small mb-2 member-bio">${user.bio}</p>
          <div class="extra-small text-muted mb-3">
            <i class="bi bi-people-fill me-1"></i>${user.sub}
          </div>
          <button class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold">Kết nối</button>
        </div>
      </div>
    </div>`;
}

function renderFollowCard(user, colClass = 'col-6 col-md-4 col-xl-3', baseUrl = window.BASE_URL) {
  const ini = initials(user.name);
  const verifiedBadge = user.verified
    ? ' <i class="bi bi-patch-check-fill text-primary"></i>' : '';
  return `
    <div class="${colClass}">
      <div class="member-card border rounded-3 overflow-hidden position-relative h-100 bg-white shadow-sm">
        <button class="btn-close-member"><i class="bi bi-x-lg"></i></button>
        <div ${bannerStyle(user.banner)}></div>
        <div class="text-center px-2 pb-3">
          <div class="member-avatar">
            <img src="${baseUrl}public/assets/images/${user.img}.jpg" alt="${user.name}"
                 class="w-100 h-100 object-fit-cover"
                 onerror="this.style.display='none';this.parentElement.innerHTML='${ini}';">
          </div>
          <h6 class="fw-bold mb-0 text-truncate">${user.name}${verifiedBadge}</h6>
          <p class="text-muted extra-small mb-1 member-bio">${user.bio}</p>
          <div class="extra-small text-muted mb-3">${user.sub}</div>
          <button class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Theo dõi
          </button>
        </div>
      </div>
    </div>`;
}

// Mount functions
document.getElementById('mayKnowGrid').innerHTML =
  mayKnowUsers.map(u => renderConnectCard(u, 'col-6 col-md-4 col-xl-4')).join('');

document.getElementById('popularGrid').innerHTML =
  popularUsers.map(u => renderFollowCard(u, 'col-6 col-md-4 col-xl-3')).join('');

document.getElementById('suggestedGrid').innerHTML =
  suggestedUsers.map(u => renderConnectCard(u, 'col-6 col-md-4 col-xl-3')).join('');

// Dismiss card
document.addEventListener('click', e => {
  const btn = e.target.closest('.btn-close-member');
  if (btn) btn.closest('[class^="col-"]').remove();
});