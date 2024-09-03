import javax.swing.JFrame;

public class App {
    public static void main(String[] args) throws Exception {
        //size of the game board
        int boardiwidth=360;
        int boardheight=640;
        
        JFrame frame= new JFrame("Flappy Bird");//window title
        frame.setVisible(true);
        frame.setSize(boardiwidth, boardheight);//get size from initial values
        frame.setLocationRelativeTo(null);//opens the window in the center of the screen
        frame.setResizable(false); //user cannot resize the window
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);//window closes after clicking close button

        Flappybird flappyBird=new Flappybird();
        frame.add(flappyBird);
        frame.pack();//for excluding the top window bar(actual size of the frame)
        flappyBird.requestFocus();
        frame.setVisible(true);
    }
}
