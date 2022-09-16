<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- --Libreria CanvasConfetti-- -->
        <script src="https://cdn.jsdelivr.net/npm/js-confetti@latest/dist/js-confetti.browser.js"></script>

        <!-- Libreria Alpine-- -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- //--libreria tailwindcss-- -->
        <script src="https://cdn.tailwindcss.com/"></script>

        <!-- --Libreria de iconos FontAwesome-- -->
        <script src="https://kit.fontawesome.com/ee812988e3.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

        <!-- --link de mi archivo css-- -->
        <link href="{{ asset('css/app.css')}}" rel="stylesheet">
        <title>MemoryCard</title>
        <style>
            /*Importacion de googleFonts*/
            @import url('https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap');
            *{
                font-family: 'Dancing Script', cursive;
            }
            #id_carta:hover{
                box-shadow: inset 0px 0px 10px #000;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <!-- --El x-data declara el scope, le pasamos una función en la cual definimos nuestra variable cartas-- -->
        <div x-data="juego()" class="m-auto">
            <h1 class="text-3xl mt-2 text-center">
                <!-- --x-text es un atributo el cual nos pintará el valor de una variable o funcion. -- -->
                <span x-text="intentos"></span>
                Intentos
            </h1>

            <!-- --imprimimos las cartas-- -->
            <div class="grid grid-cols-3 gap-10 w-3/4 mx-auto my-4 items-center justify-items-center">
                <!-- --La etiqueta template significa que no se renderiza cuando se carga una página, pero que posteriormente puede ser instanciado -- -->
                <!-- --El atributo x-for de alpine.js es para que se comporte como un ciclo for y genere una carta para cada elemento de nuestro objeto json-- -->
                <template x-for="(carta, index) in cartas" :key="index">
                    <div id="id_carta" style="min-width:85px" class="bg-gradient-to-r from-purple-300 to-violet-900  rounded w-3/4 min-w-max p-2 h-24" @click="voltear(carta)">
                        <!-- -- <button class="w-20 h-16 flex justify-center justify-items-center content-center">-- -->
                        <button class="w-full h-full">
                            <!-- --Con x-bind definimos un atributo dinamicamente en este caso es el atributo class-- -->
                            <!-- --Si el atributo volteada es true entonces mostramos el icono de la carta, si no vacío-- -->
                            <i   class="fa-3x text-white" x-bind:class="(carta.volteada ? carta.icon : '')"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>   

        <!-- --Modal para cuando gana el juego-- -->
        <div id="id_modal" x-data="{abrir:false,texto:''}" x-show="abrir"  class=" flex gap-2.5 justify-self-center p-4  sm:justify-self-end md:mr-2">
            <!-- --Contenido del modal-- -->
            <div class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center px-4 md:px-0">
                <!-- --Botón de cierre del modal-- -->
                <div @click.away="cerrarM()" class="relative bg-white rounded-lg shadow dark:bg-gray-700 w-2/4 h-auto">
                    <button @click="cerrarM()" type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="popup-modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <!-- --mensaje y botones del modal-- -->
                    <div class=" p-6 text-center flex flex-col h-full">
                        <h3 x-text="texto" class="text-3xl  mb-5 font-normal text-gray-500 dark:text-gray-400">Has Ganado</h3>
                        <button 	@click="jugarNew()" type="button" class="text-3xl self-center text-white bg-purple-500 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 dark:focus:ring-red-800 font-medium rounded-lg items-center px-5 py-2.5 text-center mr-2">
                            Jugar de nuevo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- --Contador y mensaje-- -->
        <div class="flex gap-6 sm:ml-28 text-3xl">
            <p class=" inline" id="contador">Tiempo : </p>
            <!-- Dialogo al encontrar una pareja -->
            <!-- --En el atributo data se define la variable show( la cual muestra u oculta elementos cuando show sea true o false respectivamente)-- -->
            <div id="dialogo" x-data="{show:true, message:''}" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">
                <h1 x-text="message"></h1>
            </div>
        </div>

    <script>
        function juego() {
            return {
                /*Escribimos un objeto JSON, que es una lista de las cartas con su respectivo icono y estados*/
                cartas: [
                    { icon: 'fa-solid fa-baseball-bat-ball', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-baseball-bat-ball', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-medal', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-medal', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-bicycle', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-bicycle', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-staff-snake', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-staff-snake', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-dog', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-dog', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-cat', volteada: false, encontrada: false },
                    { icon: 'fa-solid fa-cat', volteada: false, encontrada: false },
                ].sort(() => Math.random() - .5),
                /*intentos es una variable más del objeto json*/
                intentos:0,
                /*La funcion sort con random nos ordenará las cartas aleatoriamente*/
                /*esta función recibe un objeto como parámetro. Lo que hace es cambiar la propiedad volteada a TRUE. La agregamos a traves de @click.*/
                voltear(carta){
                    carta.volteada = true;
                    /*Si las cartas volteadas son 2 entonces hay que comprobar si son iguales o no*/
                    if(this.cartasVolteadas.length==2){
                        /*como ha levantado dos cartas le añadimos un intento*/
                        this.intentos++;
                        if(this.cartasVolteadas[0].icon == this.cartasVolteadas[1].icon){
                            this.mensaje("¡Has encontrado una pareja!");
                            /*los iconos de las cartas son iguales, por lo cual hay que asignarle TRUE en encontrada*/
                            this.cartasVolteadas.forEach(carta => carta.encontrada = true);
                            if (this.cartasEnJuego.length==0){
                                myStopFunction();
                                this.modalgn("¡¡Has ganado!!");
                                /*Volvemos a empezar el juego, ponemos que ninguna carta esté dada la vuelta y ninguna esté encontrada*/
                                this.baraja.forEach(carta=> {carta.volteada = false; carta.encontrada = false})
                            }
                        }else{
                            /*Si las cartas no son iguales utilizamos la funcion setTimeout que a los 500ms voltea de nuevo la carta*/
                            setTimeout(() =>{
                                this.cartasVolteadas.forEach(carta => carta.volteada = false);
                            }, 500);
                        }
                    }
                },
                /*Ahora vamos a definir unas propiedades que nos van a devolver un valor, son funciones que las definimos con la palabra get*/
                get cartasVolteadas() {
                    /*Retorna cada carta que volteada = true (que se vea el icono) y que no este encontrada, encontrada=false*/
                    return this.cartas.filter(carta => (carta.volteada && !carta.encontrada));
                },

                get cartasEnJuego(){
                    /*Retorna todas las cartas que encontrada=false, osea no estan emparejadas, servirá para saber si el juego terminó*/
                    return this.cartas.filter(carta => (!carta.encontrada));
                },

                get baraja(){
                    /*Devuelve todas las cartas, sin filtros*/
                    return this.cartas;
                },

                /*esta es la funcion para mostrar el mensaje en el html*/
                mensaje(msg){
                    console.log(  msg );
                    /*Podemos entrar desde JS a las variables de Alpine a través de "_x_dataStack[0].variable"*/
                    /**/
                    document.getElementById('dialogo')._x_dataStack[0].show = 1;
                    document.getElementById('dialogo')._x_dataStack[0].message = msg;
                    setInterval(() => {
                        document.getElementById('dialogo')._x_dataStack[0].show = 0;
                    }, 2500);
                },

                modalgn(win){
                    console.log(win);
                    confetiFY();
                    document.getElementById('id_modal')._x_dataStack[0].abrir = 1;
                    document.getElementById('id_modal')._x_dataStack[0].texto = win;
                }
            }
        };

        function cerrarM(){
            document.getElementById('id_modal')._x_dataStack[0].abrir = 0;
        }
        function jugarNew(){
            window.location.reload();
        }

        /****************Para poner el contador****************/
            let n = 0;
            let l = document.getElementById("contador");
            function contar(){
                l.innerHTML = "Tiempo: " + n;
                n++;
            }
            const Interval =window.setInterval(contar,1000);
            function myStopFunction() {
                clearInterval(Interval);
            }


        /*************CONFETI**************/
            const jsConfetti = new JSConfetti();
            function confetiFY(){
                jsConfetti.addConfetti();
            }
    </script>
    </body>
</html>
