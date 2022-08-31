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
        var player2 = null;
        var paredes = null;
        var direccion = 1;
        var puntos = 0;
        var puntuacion = 0;
        var tiempo = 0;
        var speed = 4;
        var pause = false;
        var win = false;

        var bot = new Image();
        var wallx = new Image();
        var wally = new Image();
        var gear = new Image();

        var tiempoInicio;
        var tiempoTotal = 0;
        var tiempoPausa = 0;

        var sound = new Audio();
        
        function run()
        {
            canvas = document.getElementById('cvs');
            ctx = canvas.getContext('2d');
            
            bot.src = 'img/Player.png';
            wallx.src = 'img/platformX.png';
            wally.src = 'img/platformY.png';
            gear.src = 'img/gear.png';

            sound.src = 'audio/recolectar.wav';

            player1 = new Cuadro(50,100,38,40,bot);
            player2 = new Cuadro(300,300,28,28,gear);
            paredes = [new Cuadro(80,250,25,101,wally), 
                    new Cuadro(720,250,25,101,wally),  
                    new Cuadro(350,80,101,25,wallx), 
                    new Cuadro(350,500,101,25,wallx)];
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


            /*if(tiempo > 2) {
                pause = true;
                win = true;
            }*/

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

            player1.paint(ctx);

            if(player1.se_tocan(player2)){
                sound.pause();
                sound.currentTime = 0;
                sound.play();
                player2.x = Math.random()* 500 + 100;
                player2.y = Math.random()* 200 + 200;
                puntos += 50;
            }
            player2.paint(ctx);

            if(pause && !win){
                ctx.fillStyle = "rgba(0,0,0,0.6)";
                ctx.fillRect(0,0,1350,600);
                
                ctx.font = "20px Arial";
                ctx.fillStyle = "rgb(255,255,255)";
                ctx.fillText("Pausa", 675, 320);
            }
            if(pause && win){
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
        }

        function bounds(){
            if(player1.x > 1290) player1.x = 1290;
            if(player1.y > 550) player1.y = 550;
            if(player1.x < 10) player1.x = 10;
            if(player1.y < 60) player1.y = 60;
            if(player2.x > 1290) player2.x = 1290;
            if(player2.y > 550) player2.y = 550;
            if(player2.x < 10) player2.x = 10;
            if(player2.y < 60) player2.y = 60;
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
