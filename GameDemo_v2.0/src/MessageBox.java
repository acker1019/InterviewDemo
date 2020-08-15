import java.awt.Color;
import java.awt.Font;

public class MessageBox extends GameObject {

	public MessageBox(String text, boolean trans, String fontName, int style, int fontSize, Color fontColor, Color bg) {
		super(null, 0);
		setText(text);
		setBackground(bg);
		setOpaque(!trans);
		setForeground(fontColor);
		setFont(new Font(fontName, style, fontSize));
	}//cons.

	@Override
	protected void loadImg(String filename, int count) {
		//empty
	}//loadImg

}//MessageBox
