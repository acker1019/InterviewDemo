import java.io.IOException;
import java.util.Random;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;

public class Item extends GameObject {
	private ImageIcon state[];

	public Item(String filename, int count) {
		super(filename, count);
	}// cons.

	@Override
	protected void loadImg(String filename, int count) {
		state = new ImageIcon[count];
		try {
			for (int i = 0; i < count; i++) {
				state[i] = new ImageIcon(ImageIO.read(Demo.class.getResource("/img/" + filename + i + ".png")));
			} // for
			setIcon(state[0]);
		} catch (IOException e) {
			System.out.println("檔案不存在");
		} // try-catch
	}//loadImg

	public void vibrate(int count) {
		int centerX = x;
		int centerY = y;
		Random ran = new Random();
		for(int i = 0 ; i < count ; i++) {
			int cx  = ran.nextInt(5) + 1;
			if(x > centerX) cx = -cx;
			int cy  = ran.nextInt(5) + 1;
			if(y > centerY) cy = -cy;
			move(cx, cy, 10);
		}//for
		setPos(centerX, centerY);
	}// setPos

	public void setState(int idx) {
		setIcon(state[idx]);
	}//setState
}// Character
