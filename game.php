<!DOCTYPE html>
<html>
    <head>
        <title>Canvas</title>
    </head>
    <body>
        <canvas width="1335" height="600" id="cvs">
          Hola tu navegador no funciona
        </canvas>

        <script>
        var canvas = null;
        var ctx = null;
        var player1 = null;
        var gears = null;
        var paredes = null;
        var station = null;
        var direccion = 1;
        var puntos = 0;
        var puntuacion = 0;
        var tiempo = 0;
        var speed = 0;
        var pause = false;
        var win = false;

        var bot = new Image();
        var wallx = new Image();
        var wally = new Image();
        var gear = new Image();
        var charge = new Image();

        var tiempoInicio;
        var tiempoTotal = 0;
        var tiempoPausa = 0;

        var gearSound = new Audio();
        var music = new Audio();
        var victory = new Audio();
        
        function run()
        {
            canvas = document.getElementById('cvs');
            ctx = canvas.getContext('2d');
            
            bot.src = 'img/Player.png';
            wallx.src = 'img/platformX.png';
            wally.src = 'img/platformY.png';
            gear.src = 'img/gear.png';
            charge.src = 'img/chargestation.png';

            gearSound.src = 'audio/recolectar.wav';
            music.src = 'audio/bgMusic.wav';
            gearSound.src = 'audio/recolectar.wav';
            victory.src = 'audio/victory.mp3';

            player1 = new Cuadro(60,75,38,40,bot);
            gears = [new Cuadro(300,300,28,28,gear), new Cuadro(500,500,28,28,gear)];
            paredes = [new Cuadro(0,60,25,101,wally), 
                    new Cuadro(0,161,25,101,wally),
                    new Cuadro(0,262,25,101,wally),
                    new Cuadro(0,363,25,101,wally),
                    new Cuadro(0,464,25,101,wally),
                    new Cuadro(0,565,25,101,wally),

                    new Cuadro(1310,60,25,101,wally), 
                    new Cuadro(1310,161,25,101,wally),
                    new Cuadro(1310,262,25,101,wally),
                    new Cuadro(1310,363,25,101,wally),
                    new Cuadro(1310,464,25,101,wally),
                    new Cuadro(1310,565,25,101,wally),

                    new Cuadro(126,60,101,25,wallx),
                    new Cuadro(227,60,101,25,wallx),
                    new Cuadro(328,60,101,25,wallx),
                    new Cuadro(429,60,101,25,wallx),
                    new Cuadro(530,60,101,25,wallx),
                    new Cuadro(631,60,101,25,wallx),
                    new Cuadro(732,60,101,25,wallx),
                    new Cuadro(833,60,101,25,wallx),
                    new Cuadro(934,60,101,25,wallx),
                    new Cuadro(1035,60,101,25,wallx),
                    new Cuadro(1136,60,101,25,wallx),
                    new Cuadro(1237,60,101,25,wallx),
                
                    
                    new Cuadro(0,575,101,25,wallx),
                    new Cuadro(101,575,101,25,wallx),
                    new Cuadro(202,575,101,25,wallx),
                    new Cuadro(303,575,101,25,wallx),
                    new Cuadro(404,575,101,25,wallx),
                    new Cuadro(505,575,101,25,wallx),
                    new Cuadro(606,575,101,25,wallx),
                    new Cuadro(707,575,101,25,wallx),
                    new Cuadro(808,575,101,25,wallx),
                    new Cuadro(909,575,101,25,wallx),
                    new Cuadro(1010,575,101,25,wallx),
                    new Cuadro(1111,575,101,25,wallx)];
                    
            station = new Cuadro(1210,525,101,72,charge);
            paint();
        }

        window.requestAnimationFrame = (function () {
        return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            function (callback) {
                window.setTimeout(callback, 17);
            };
        }());

        window.addEventListener('load', run(), false);

        document.addEventListener("keydown", function(e){
            console.log(e.keyCode);
            if(speed == 0) speed = 4;
            if(!pause){
                if(e.keyCode == 37 || e.keyCode == 65){
                    direccion = 2;
                }
                if(e.keyCode == 38 || e.keyCode == 87){
                    direccion = 4;
                }
                if(e.keyCode == 39 || e.keyCode == 68){
                    direccion = 1;
                }
                if(e.keyCode == 40 || e.keyCode == 83){
                    direccion = 3;
                }
            }
            if(e.keyCode == 32 && !win){
                pause = !pause;
            }
            if(e.keyCode == 82){
                location.reload();
            }
        });



        function paint(currentTime){

            if(!win)music.play();

            if(!tiempoInicio) tiempoInicio = currentTime;

            if(!pause){
                tiempoTotal = (currentTime - tiempoInicio) - tiempoPausa;
            }else if(pause){
                tiempoPausa = (currentTime - tiempoInicio) - tiempoTotal;
                tiempoTotal = (currentTime - tiempoInicio) - tiempoPausa;
            }

            
            tiempo = (tiempoTotal/1000).toFixed(1);
            puntuacion = (puntos-(tiempo*5)).toFixed(0);
            if(puntuacion < 0) puntuacion = 0;


            if(player1.se_tocan(station) && !win){
                pause = true;
                win = true;
                music.pause();
                victory.play();
            }

            window.requestAnimationFrame(paint);
            ctx.fillStyle = "rgb(153, 113, 166)";
            ctx.fillRect(0,0,1335,600);
            
           
            bounds();

            if(!pause){
                ctx.font = "20px Arial";
                ctx.fillStyle = "rgb(255,255,255)";
                ctx.fillText("Tiempo: " + tiempo + 's', 25, 25);
                if(puntuacion < 60) ctx.fillStyle = "rgb(255,138,138)";
                ctx.fillText("Puntuación: " + puntuacion + " puntos", 25, 50);
                move();
            }

            for (var i = 0; i < paredes.length; i++) {
                paredes[i].paint(ctx);
                if(player1.se_tocan(paredes[i])){
                    switch(direccion){
                        case 1:
                            player1.x -= speed;
                        break;
                        case 2:
                            player1.x += speed;
                        break;
                        case 3:
                            player1.y -= speed;
                        break;
                        case 4:
                            player1.y += speed;
                        break;
                    }
                }
            }

            for (var i = 0; i < gears.length; i++) {
                gears[i].paint(ctx);
                if(player1.se_tocan(gears[i])){
                    gearSound.pause();
                    gearSound.currentTime = 0;
                    gearSound.play();
                    puntos += 50;
                    gears.splice(i, 1);
                }
            }

            station.paint(ctx);
            player1.paint(ctx);

            if(pause && !win){
                pausa();
            }
            if(pause && win){
                winscreen();
            }
        }

        function pausa(){
            ctx.fillStyle = "rgba(0,0,0,0.6)";
            ctx.fillRect(0,0,1350,600);
            
            ctx.font = "20px Arial";
            ctx.fillStyle = "rgb(255,255,255)";
            ctx.fillText("Pausa", 675, 320);
        }

        function winscreen(){
            ctx.fillStyle = "rgba(138,138,209,0.4)";
            ctx.fillRect(0,0,1350,600);
            
            ctx.font = "50px Arial";
            ctx.fillStyle = "rgb(255,255,255)";
            ctx.fillText("¡Has ganado!", 505, 290);
            ctx.font = "30px Arial";
            ctx.fillText("Tiempo: " + tiempo + 's', 570 , 335);
            ctx.fillText("Puntuación: " + puntuacion + " puntos", 520, 370);
            ctx.font = "15px Arial";
            ctx.fillText("Presiona R para reiniciar", 570, 415);
        }

        function bounds(){
            if(player1.x > 1290) player1.x = 1290;
            if(player1.y > 550) player1.y = 550;
            if(player1.x < 10) player1.x = 10;
            if(player1.y < 60) player1.y = 60;
        }

        function move(){
            switch(direccion){
                case 1:
                    player1.x += speed;
                break;
                case 2:
                    player1.x -= speed;
                break;
                case 3:
                    player1.y += speed;
                break;
                case 4:
                    player1.y -= speed;
                break;
            }
        }

        function Cuadro(x, y, w, h, src){
            this.x = x;
            this.y = y;
            this.w = w;
            this.h = h;
            this.src = src;

            this.paint = function(ctx){
                ctx.drawImage(this.src, this.x, this.y, this.w, this.h);
            }

            this.se_tocan = function (target) { 
                if(this.x < target.x + target.w &&
                this.x + this.w > target.x && 
                this.y < target.y + target.h && 
                this.y + this.h > target.y){
                    return true;
                }
            };
        }
        </script>
    </body>
</html>
