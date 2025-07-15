// ===== GREETING + DATE =====
function setGreeting() {
  const greetingEl = document.getElementById('greeting');
  const dateEl = document.getElementById('current-date');
  const now = new Date();
  const hours = now.getHours();
  let greeting = 'Hello';

  if (hours < 12) {
    greeting = 'Good Morning, Chef!';
  } else if (hours < 18) {
    greeting = 'Good Afternoon, Chef!';
  } else {
    greeting = 'Good Evening, Chef!';
  }

  greetingEl.textContent = greeting;
  dateEl.textContent = now.toDateString();
}
setGreeting();

// ===== NAV LINK ACTIVE HIGHLIGHT ON SCROLL =====
const sections = document.querySelectorAll("section");
const navLinks = document.querySelectorAll("nav ul li a");

window.addEventListener("scroll", () => {
  let current = "";
  sections.forEach((section) => {
    const sectionTop = section.offsetTop - 100;
    if (pageYOffset >= sectionTop) {
      current = section.getAttribute("id");
    }
  });

  navLinks.forEach((link) => {
    link.classList.remove("active");
    if (link.getAttribute("href").includes(current)) {
      link.classList.add("active");
    }
  });
});
// ===== Show/Hide Cooking Instructions =====
const showStepButtons = document.querySelectorAll('.show-steps-btn');

showStepButtons.forEach((btn) => {
  btn.addEventListener('click', () => {
    const instructions = btn.previousElementSibling;
    if (instructions.style.display === 'block') {
      instructions.style.display = 'none';
      btn.textContent = 'Show Steps';
    } else {
      instructions.style.display = 'block';
      btn.textContent = 'Hide Steps';
    }
  });
});
// ===== Cooking Tip of the Day =====
const tips = [
  'Always taste as you cook.',
  'Let meat rest before cutting to retain juices.',
  'Use sharp knives for better safety and precision.',
  'Clean as you go for better workflow.',
  'Season your food properly for best flavor.'
];

document.getElementById('tip-btn').addEventListener('click', () => {
  const tipBox = document.getElementById('tip-box');
  const randomTip = tips[Math.floor(Math.random() * tips.length)];

  // Random background color for fun
  const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16);
  tipBox.style.backgroundColor = randomColor;
  tipBox.style.color = '#fff';
  tipBox.textContent = randomTip;
});
// ===== Meal Planner Form Submission =====
const form = document.getElementById('planner-form');
const tableBody = document.querySelector('#planner-table tbody');

form.addEventListener('submit', function(e) {
  e.preventDefault();

  const mealName = form.mealName.value.trim();
  const day = form.day.value;
  const types = Array.from(form.querySelectorAll('input[name="type"]:checked')).map(cb => cb.value);

  // === Validation ===
  let errors = [];

  if (mealName.length < 3) {
    errors.push('Meal name must be at least 3 characters.');
  }
  if (types.length === 0) {
    errors.push('Select at least one meal type.');
  }
  if (!day) {
    errors.push('Select a day.');
  }

  if (errors.length > 0) {
    alert(errors.join('\n'));
    return;
  }

  // === Check existing meals for the day ===
  const mealsForDay = tableBody.querySelectorAll(`tr[data-day="${day}"]`);
  if (mealsForDay.length >= 3) {
    alert('This day already has 3 meals planned.');
    return;
  }

  // === Insert meal into table ===
  const newRow = document.createElement('tr');
  newRow.setAttribute('data-day', day);
  newRow.innerHTML = `
    <td>${day}</td>
    <td>${mealName}</td>
    <td>${types.join(', ')}</td>
  `;
  tableBody.appendChild(newRow);

  form.reset();
});
