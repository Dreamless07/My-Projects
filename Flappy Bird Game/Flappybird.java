import java.awt.*;
import java.awt.event.*;
import java.util.ArrayList;
import java.util.Random;
import java.util.Random.*;
import javax.swing.*;

public class Flappybird extends JPanel implements ActionListener, KeyListener{//actionlistener for the "this" command performs action,keylistener for recognizing key press
    int boardwidth=360;
    int boardheight=640;

     //all images for background
     Image backgroundImg;
     Image birdImg;
     Image topPipeImg;
     Image bottomPipeImg;

     //bird Image
     int birdX=boardwidth/8;//location of bird horizontally
     int birdY=boardheight/2;//location of bird vertically
     int birdwidth=34;
     int birdheight=24;

     class Bird{//storing the dimensions and location of the bird in a class
        int x= birdX;
        int y= birdY;
        int width=birdwidth;
        int height=birdheight;
        Image img;

        //bird image constructor   
        Bird(Image img) {
            this.img=img;
        }
     }
     //pipes image
     int pipeX=boardwidth;//starts from the right side of the window
     int pipeY=0;//length ends at top of the window
     int pipeWidth=64; //reduced both width and height by 1/6
     int pipeHeight=512;


     class Pipe{//storing dimnensions and location of pipe in class
        int x=pipeX;
        int y=pipeY;
        int width=pipeWidth;
        int height=pipeHeight;
        Image img;
        boolean passed=false;//tracks points after the bird passes the pipe

        Pipe(Image img) {//pipe image constructor
            this.img=img;
        }
     }

     //bird object
     Bird bird;
     //speed of the pipes moving from right to left
     int velocityX = -4;
     //speed of the bird 
     int velocityY = 0;//bird moves only up & down(top is -ve) starts with 0 velocity or falling down
     //gravity to the bird
     int gravity = 1;
     //array list for storing similar names like pipes etc
     ArrayList<Pipe> pipes;
     Random random= new Random();
     //for looping the background per frame drawing again again(paint fucntion)
     Timer gameloop;
     //timer for pipe placement
     Timer placePipesTimer;
     //for gameover
     boolean gameOver=false;
     //score for passing pipes
     double score=0;

     

    Flappybird(){//constructor
        setPreferredSize(new Dimension(boardwidth, boardheight));
        //setBackground(Color.blue);
        setFocusable(true);//main function for the key events
        addKeyListener(this);//checks for spacebar pressed or not

        //load the above images into the constructor
        backgroundImg= new ImageIcon(getClass().getResource("./flappybirdbg.png")).getImage();
        birdImg= new ImageIcon(getClass().getResource("./flappybird.png")).getImage();
        topPipeImg= new ImageIcon(getClass().getResource("./toppipe.png")).getImage();
        bottomPipeImg= new ImageIcon(getClass().getResource("./bottompipe.png")).getImage();

        //load the above bird images into constructor
        bird= new Bird(birdImg);
        pipes=new ArrayList<Pipe>(); 

        //place pipes timer
        placePipesTimer= new Timer (1500,new ActionListener() {//creating new action listener because its already present for bgtimer 
            @Override
            public void actionPerformed(ActionEvent e){
                placePipes();//places pipes every 1.5 seconds 
            }
        });
        //start the pipe timer
        placePipesTimer.start();

        //load loop timer object
        gameloop= new Timer(1000/60,this);//loops 60 times for 60fps 1000ms=1s
        //Start the bg timer
        gameloop.start();

    }

    public void placePipes() {
        int randomPipeY= (int) (pipeY - pipeHeight/4 - Math.random()*(pipeHeight/2));//for random pipe heights
        int openingSpace = boardheight/4;

        Pipe topPipe= new Pipe(topPipeImg);
        topPipe.y=randomPipeY;
        pipes.add(topPipe);

        Pipe bottomPipe= new Pipe(bottomPipeImg);
        bottomPipe.y=topPipe.y+pipeHeight+openingSpace;
        pipes.add(bottomPipe);
    }

    public void paintComponent(Graphics g) {
        super.paintComponent(g);
        draw(g);
    }
    
    public void draw(Graphics g) {
        //draw background
        g.drawImage(backgroundImg,0, 0, boardwidth, boardheight, null);

        //draw the bird
        g.drawImage(bird.img, bird.x, bird.y, bird.width, bird.height, null);

        //draw the pipes
        for(int i=0;i<pipes.size();i++) {
            Pipe pipe=pipes.get(i);
            g.drawImage(pipe.img, pipe.x, pipe.y, pipe.width, pipe.height, null);
        }
        //score color and font
        g.setColor(Color.white);
        g.setFont(new Font("Arial", Font.PLAIN, 32));
        //print the score on the screen after game over
        if(gameOver) {
            g.drawString("Game Over: "+ String.valueOf((int) score),10,35);
        }
        //score on screen during game
        else {
            g.drawString(String.valueOf((int) score),10,35);
        }
    }

    public void move() {
        //bird
        velocityY += gravity;//velocity gets reduced by gravity and bird falls down after reduction of velocity
        bird.y += velocityY;
        bird.y=Math.max(bird.y, 0);// bird top movement only till the top of screen ie till 0

        //pipes
        for(int i=0;i<pipes.size();i++) {
            Pipe pipe=pipes.get(i);
            pipe.x+=velocityX;//move the pipes every frame by -4 

            if(!pipe.passed && bird.x > pipe.x +pipe.width) {//if bird passes pipe fully til the right
                pipe.passed=true;
                score+=0.5;//top and bottom pipe score is 1(0.5 for either top or bottom pipe)
            }
            
            if(collision(bird, pipe)) {
                gameOver=true;
            }
        }
        if (bird.y > boardheight) {
            gameOver=true;//if the bird goes out of the screen it is gameover
        }
    }

    public boolean collision(Bird a,Pipe b) {//detects collisions
        return a.x < b.x + b.width &&
        a.x + a.width > b.x &&
        a.y < b.y + b.height &&
        a.y + a.height > b.y;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        move();//bird moves then background repaints 60 times per second
        repaint();
        if (gameOver){//if it is gameover then both timers stop
            placePipesTimer.stop();
            gameloop.stop();
        }
    }

    

    @Override
    public void keyPressed(KeyEvent e) {
        // for all keys(backspace,f5 etc)
        if (e.getKeyCode() == KeyEvent.VK_SPACE) {//only when space bar is pressed
            velocityY = -9;//resets the velocity after space is pressed 
            if(gameOver) {//for restarting after gameover
                bird.y=birdY;
                velocityY=0;
                pipes.clear();
                score=0;
                gameOver=false;
                gameloop.start();
                placePipesTimer.start();


            }
        }
    }

    @Override
    public void keyTyped(KeyEvent e) {}//should be a character(not like f5 key etc)

    @Override
    public void keyReleased(KeyEvent e) {}// when any key is released
}
