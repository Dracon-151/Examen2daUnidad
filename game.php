<!DOCTYPE html>
<html>
    <head>
        <title>Canvas</title>
    </head>
    <body>
        <canvas width="800" height="600" id="cvs">
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
        var speed = 5;
        var pause = false;

        var bot = new Image();
        var wallx = new Image();
        var wally = new Image();
        var gear = new Image();

        var sound = new Audio();
        
        function run()
        {
            canvas = document.getElementById('cvs');
            ctx = canvas.getContext('2d');
            
            bot.src = 'img/PlayerSprite.png';
            wallx.src = 'img/platformX.png';
            wally.src = 'img/platformY.png';
            gear.src = 'img/gear.png';

            sound.src = 'audio/gearSound.mp3';

            player1 = new Cuadro(200,200,38,40,bot);
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
            if(e.keyCode == 32){
                pause = !pause;
            }
        });



        function paint(){

            window.requestAnimationFrame(paint);
            ctx.fillStyle = "rgb(255,255,255)";
            ctx.fillRect(0,0,800,600);
            
            ctx.font = "20px Arial";
            ctx.fillStyle = "rgb(100,100,100)";
            ctx.fillText("Puntos: " + puntos, 50, 50);

            bounds();

            if(!pause){
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
                puntos += 10;
                speed += 0.5;
            }
            player2.paint(ctx);

            if(pause){
                ctx.fillStyle = "rgba(0,0,0,0.4)";
                ctx.fillRect(0,0,800,600);
                
                ctx.font = "20px Arial";
                ctx.fillStyle = "rgb(255,255,255)";
                ctx.fillText("Pausa", 350, 320);
            }
        }

        function bounds(){
            if(player1.x > 800) player1.x = 0;
            if(player1.y > 600) player1.y = 0;
            if(player1.x < 0) player1.x = 800;
            if(player1.y < 0) player1.y = 600;
            if(player2.x > 800) player2.x = 0;
            if(player2.y > 600) player2.y = 0;
            if(player2.x < 0) player2.x = 800;
            if(player2.y < 0) player2.y = 600;
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
