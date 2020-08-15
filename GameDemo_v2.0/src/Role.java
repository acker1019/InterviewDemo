import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.io.IOException;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;

public class Role extends GameObject {
	private ImageIcon rstand;
	private ImageIcon lstand;
	private ImageIcon rrun[];
	private ImageIcon lrun[];

	private Demo context;

	private boolean keepListen;
	private int addx;
	private int speed;
	private int tarX;

	public Role(String filename, int count, Demo context) {
		super(filename, count);
		this.context = context;
		addMouseController();
		addKeyController();
		keepListen = false;
		addx = 0;
	}//cons.

	@Override
	protected void loadImg(String filename, int count) {
		rrun = new ImageIcon[count];
		lrun = new ImageIcon[count];
		try {
			rstand = new ImageIcon(ImageIO.read(Demo.class.getResource("/img/" + filename + "_rstand.png")));
			lstand = new ImageIcon(ImageIO.read(Demo.class.getResource("/img/" + filename + "_lstand.png")));
			for (int i = 0; i < count; i++) {
				rrun[i] = new ImageIcon(ImageIO.read(Demo.class.getResource("/img/" + filename + "_rrun" + i + ".png")));
			} // for
			for (int i = 0; i < count; i++) {
				lrun[i] = new ImageIcon(ImageIO.read(Demo.class.getResource("/img/" + filename + "_lrun" + i + ".png")));
			} // for
			setIcon(rstand);
		} catch (IOException e) {
			System.out.println("檔案不存在");
		} // try-catch
	}//loadImg

	public void move(int speed, int dist, int changeCount) {
		if(speed > 0) {
			setIcon(rstand);
		} else if(speed < 0) {
			setIcon(lstand);
		}//else-if
		int moveTime = 20;
		int moveCount = dist / Math.abs(speed);
		int actCount = 0;
		for(int i = 0 ; i < moveCount ; i++) {
			setPos(x + speed, y);
			try { Thread.sleep(moveTime); } catch (InterruptedException e) {}
			if(i % changeCount == 0) {
				if(speed > 0) {
					setIcon(rrun[actCount]);
				} else if(speed < 0) {
					setIcon(lrun[actCount]);
				}//else-if
				actCount = (actCount+1) % 6;
			}//if
		}//for
	}// move

	public void listenCmd(int speed, int changeCount) {
		this.speed = Math.abs(speed);
		keepListen = true;
		int moveTime = 20;
		int frameCount = 0;
		int actCount = 0;
		while(keepListen) {
			setPos(x + addx, y);
			try { Thread.sleep(moveTime); } catch (InterruptedException e) {}
			if(frameCount % changeCount == 0) {
				if(addx > 0) {
					setIcon(rrun[actCount]);
				} else if(addx < 0) {
					setIcon(lrun[actCount]);
				}//else-if
				actCount = (actCount+1) % 6;
			}//if
			frameCount = (frameCount + 1) % Integer.MAX_VALUE;
			if(addx < 0 && x <= tarX-25) {
				addx = 0;
				setIcon(lstand);
			} else if(addx > 0 && x >= tarX-25) {
				addx = 0;
				setIcon(rstand);
			}//else-if
			keepListen = context.checkCollision();
		}//while
		stopCmd();
	}// listenCmd

	public void stopCmd() {
		keepListen = false;
		setIcon(rstand);
		addx = 0;
		tarX = x;
	}//stopCmd

	protected void addMouseController() {
		context.cpanel.addMouseListener(new MouseAdapter() {
			@Override
			public void mouseClicked(MouseEvent e) {
				if(e.getButton() == MouseEvent.BUTTON1) {
					tarX = e.getX();
					int dist = tarX - x;
					if(e.getClickCount() == 1) {
						System.out.println("mouse left-clicked at x: " + tarX);
						addx = (dist > 0) ? speed : -speed;
					} else if (e.getClickCount() == 2) {
						System.out.println("mouse double left-clicked at x: " + tarX);
						addx = (dist > 0) ? 4*speed : -4*speed;
					}//if
				} else if(e.getButton() == MouseEvent.BUTTON2) {
					System.out.println("mouse middle-clicked at x: " + e.getX());
					if(e.getClickCount() == 2) {
						System.out.println(" -- and it is a double middle-click");
					}//if
				} else if(e.getButton() == MouseEvent.BUTTON3) {
					System.out.println("mouse right-clicked at x: " + e.getX());
					if(e.getClickCount() == 2) {
						System.out.println(" -- and it is a double right-click");
					}//if
				}//else-if
			}//mouseClicked
		});//addMouseListener
	}//addMouseController

	protected void addKeyController() {
		context.addKeyListener(new KeyAdapter() {
			@Override
			public void keyPressed(KeyEvent e) {
				System.out.println(e);
				switch(e.getKeyCode()) {
					case KeyEvent.VK_LEFT:
						tarX = 0;
						addx = -speed;
						break;
					case KeyEvent.VK_RIGHT:
						tarX = Integer.MAX_VALUE;
						addx = speed;
						break;
				}//switch
			}//keyPressed

			@Override
			public void keyReleased(KeyEvent e) {
				System.out.println(e);
				switch(e.getKeyCode()) {
					case KeyEvent.VK_LEFT:
						addx = 0;
						setIcon(lstand);
						break;
					case KeyEvent.VK_RIGHT:
						addx = 0;
						setIcon(rstand);
						break;
				}//switch
			}//keyReleased
		});//addKeyListener
	}//addKeyController
}//Role2
