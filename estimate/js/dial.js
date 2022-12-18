document.addEventListener('DOMContentLoaded', function() {
    let arrayCanvas = getElements();
    if (!!arrayCanvas) {
        drawDials(arrayCanvas);
    }
}, false);

jQuery( document ).ajaxComplete(function() {
    let arrayCanvas = getElements();
    if (!!arrayCanvas) {
        drawDials(arrayCanvas);
    }
});

function getElements(){
    let arrayCanvas = [];
    let i = 1;
    for (i;i <= 12;i++){
        let canvas = document.getElementById("canvas-" + i)
        if (canvas !== null){
            arrayCanvas.push(canvas);
        }
    }
    return arrayCanvas;
}

function drawDials(arrayCanvas){
    arrayCanvas.forEach((canvas) => {
        var bgColor = window.getComputedStyle(document.body, null).getPropertyValue('background-color');
        var fontColor = window.getComputedStyle(document.body, null).getPropertyValue('color');
        var fontFamily = window.getComputedStyle(document.body, null).getPropertyValue('font-family');

        var ctx = canvas.getContext("2d");

        // general settings
        var middleX = canvas.width / 2;
        var middleY = canvas.height / 2;
        var radius = canvas.width / 2.5 - canvas.width / 10;
        // beginning and ending of our arc. Sets by rad * pi
        var startAngleIndex = 0.75;
        var endAngleIndex = 2.25;

        // zones settings
        var zoneLineWidth = canvas.width / 30;
        // clockwise
        var counterClockwise = false;

        // ticks settings
        var tickWidth = canvas.width / 100;
        var tickColor = "#746845";
        var tickOffsetFromArc = canvas.width / 40;


        // Digits settings
        var min = document.getElementById(canvas.id + "-min").textContent ?? 0;
        var low = document.getElementById(canvas.id + "-low").textContent ?? 0;
        var high = document.getElementById(canvas.id + "-high").textContent ?? 0;
        var max = document.getElementById(canvas.id + "-max").textContent ?? 0;

        var digits = [min, low, high, max];

        var DrawZones = function () {

            var zonesminlow = ((low / max * 100) * 75) / 100;
            var zoneslowhigh = (((high - low) / max * 100) * 75) / 100;
            var zoneshighmax = (((max - high) / max * 100) * 75) / 100;

            var blackZonesCount = ((2 * Math.PI * (25 / 100)));
            var greenZonesCount = ((2 * Math.PI * (zonesminlow / 100)));
            var yellowZonesCount = ((2 * Math.PI * (zoneslowhigh / 100)));
            var redZonesCount = ((2 * Math.PI * (zoneshighmax / 100)));

            var startAngle = (startAngleIndex - 0.50) * Math.PI;
            var endBlackAngle = startAngleIndex * Math.PI;
            var endGreenAngle = endBlackAngle + greenZonesCount;
            var endYellowAngle = endGreenAngle + yellowZonesCount;
            var endRedAngle = endYellowAngle + redZonesCount;

            var sectionOptions = [
                {
                    startAngle: startAngle,
                    endAngle: endBlackAngle,
                    color: bgColor
                },
                {
                    startAngle: endBlackAngle,
                    endAngle: endGreenAngle,
                    color: "#008000"
                },
                {
                    startAngle: endGreenAngle,
                    endAngle: endYellowAngle,
                    color: "#ffff00"
                },
                {
                    startAngle: endYellowAngle,
                    endAngle: endRedAngle,
                    color: "#FF0000"
                }
            ];

            this.DrawZone = function (options) {
                ctx.beginPath();
                ctx.arc(middleX, middleY, radius, options.startAngle, options.endAngle, counterClockwise);
                ctx.lineWidth = zoneLineWidth;
                ctx.strokeStyle = options.color;
                ctx.lineCap = "butt";
                ctx.stroke();
            };

            sectionOptions.forEach(function (options) {
                DrawZone(options);
            });

            this.DrawTick = function (angle) {
                var fromX = middleX + (radius - tickOffsetFromArc) * Math.cos(angle);
                var fromY = middleY + (radius - tickOffsetFromArc) * Math.sin(angle);
                var toX = middleX + (radius + tickOffsetFromArc) * Math.cos(angle);
                var toY = middleY + (radius + tickOffsetFromArc) * Math.sin(angle);

                ctx.beginPath();
                ctx.moveTo(fromX, fromY);
                ctx.lineTo(toX, toY);
                ctx.lineWidth = tickWidth;
                ctx.lineCap = "round";
                ctx.strokeStyle = tickColor;
                ctx.stroke();
            };

            this.DrawTick(endBlackAngle);
            this.DrawTick(endGreenAngle);
            this.DrawTick(endYellowAngle);
            this.DrawTick(endRedAngle);

            this.DrawText = function (angle, digit) {

                var x = middleX + (radius + canvas.width / 12) * Math.cos(angle);
                var y = middleY + (radius + canvas.width / 12) * Math.sin(angle);

                ctx.font = "12px " + fontFamily;
                ctx.fillStyle = fontColor;
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(digit, x, y);
            };

            this.DrawText(endBlackAngle, digits[0]);
            this.DrawText(endGreenAngle, digits[1]);
            this.DrawText(endYellowAngle, digits[2]);
            this.DrawText(endRedAngle, digits[3]);
        };

        DrawZones();
    })
}






