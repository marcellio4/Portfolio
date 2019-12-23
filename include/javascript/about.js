$(document).ready(function() {
   move(document.getElementById("one"), 100);
   move(document.getElementById("two"), 95);
   move(document.getElementById("three"), 85);
   move(document.getElementById("fourth"), 95);
   move(document.getElementById("fifth"), 85);
   move(document.getElementById("sixth"), 70);
   move(document.getElementById("seventh"), 75);
   move(document.getElementById("eight"), 85);

});

// moving dynamicly progress bar in the bar chart
function move(element, new_width) {
    var elem = element;
    var width = 1;
    var id = setInterval(frame, 40);
    function frame() {
        if (width >= new_width) {
            clearInterval(id);
        } else {
            width++;
            elem.style.width = width + '%';
        }
    }
}
