const students = [
  {
    name: "Arefin",
    roll: 101,
    scores: {
      Bangla: 75,
      English: 80,
      CSE115: 90,
      MAT101: 85
    },
    attendance: true
  },
  {
    name: "Rakib",
    roll: 102,
    scores: {
      Bangla: 65,
      English: 70,
      CSE115: 60,
      MAT101: 75
    },
    attendance: false
  },
  {
    name: "Bipul",
    roll: 103,
    scores: {
      Bangla: 80,
      English: 85,
      CSE115: 88,
      MAT101: 90
    },
    attendance: true
  }
];

students.forEach(student => {
  if (!student.attendance) {
    console.log(`${student.name} (Roll: ${student.roll}): not eligible`);
  } else {
    const total = Object.values(student.scores).reduce((sum, score) => sum + score, 0);
    console.log(`${student.name} (Roll: ${student.roll}): Total Score = ${total}`);
  }
});


document.getElementById("header").innerHTML = "Student Scores and Attendance";