const container = document.getElementById("container");

    for (let i = 1; i <= 100; i++) {
      const div = document.createElement("div");
      div.classList.add("box");

      const p = document.createElement("p");
      p.textContent = `BoX ${i}`;

      
      if (i % 2 === 0) {
        div.classList.add("even");
      } else {
        div.classList.add("odd");
      }

      div.appendChild(p);
      container.appendChild(div);
    }