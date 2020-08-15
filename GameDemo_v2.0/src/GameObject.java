import javax.swing.ImageIcon;
import javax.swing.JLabel;

public abstract class GameObject extends JLabel {
	protected int x;
	protected int y;
	protected String name;
	protected ImageIcon state[];

	protected abstract void loadImg(String filename, int count);

	public GameObject(String filename, int count) {
		this.name = filename;
		loadImg(filename, count);
	}//cons.

	public int getObjX() {
		return x;
	}//getObjX

	public int getObjY() {
		return y;
	}//getObjY

	public void setPos(int x, int y) {
		this.x = x;
		this.y = y;
		setBounds(x, y, getPreferredSize().width, getPreferredSize().height);
	}// setPos

	public void move(int speedX, int speedY, int dist) {
		int moveTime = 20;
		int speed = (int)Math.sqrt(Math.pow(speedX, 2) + Math.pow(speedY, 2));
		speed = (speed>0) ? speed : 1;
		int moveCount = dist / speed;
		for(int i = 0 ; i < moveCount ; i++) {
			setPos(x + speedX, y + speedY);
			try { Thread.sleep(moveTime); } catch (InterruptedException e) {}
		}//for
	}// setPos
}//GameObject
