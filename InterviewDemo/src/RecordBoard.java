
import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.net.URL;
import java.util.List;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTabbedPane;

public class RecordBoard extends JFrame {
	// constants
	public static final int FORM_PANE = 1;
	public static final int ESSENTIAL_PANE = 2;
	
	// public
	public JLabel state_bar;
	
	// private
	private JPanel contentpane;
	private JPanel contentpane2;
	private ImageIcon piece_icon;
	
	public RecordBoard() {
		/*
		 * Constructor of tht GUI.
		 */
		
		// Define attributes of the window.
		super("Eight Queen Solution");
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		this.setSize(700, 700);
		this.setLocation(1100, 50);
		this.setResizable(false);
		this.setLayout(new BorderLayout());
		
		URL imgURL = Main.class.getResource("Elsa_Icon.png");
		this.piece_icon = new ImageIcon(imgURL);
		
		state_bar = new JLabel("Loading...");
		this.add(state_bar, BorderLayout.NORTH);
		
		// Build the components in tab "Form".
		this.contentpane = new JPanel();
		this.contentpane.setLayout(new BoxLayout(contentpane, BoxLayout.Y_AXIS));
		
		JScrollPane scrollpane = new JScrollPane(contentpane);
		scrollpane.getVerticalScrollBar().setUnitIncrement(25);
		
		JTabbedPane tabbedpane = new JTabbedPane();
		tabbedpane.addTab("Form", scrollpane);

		// Build the components in tab "Essential".
		this.contentpane2 = new JPanel();
		this.contentpane2.setLayout(new BoxLayout(contentpane2, BoxLayout.Y_AXIS));
		
		scrollpane = new JScrollPane(contentpane2);
		scrollpane.getVerticalScrollBar().setUnitIncrement(25);
		
		tabbedpane.addTab("Essential", scrollpane);
		
		this.add(tabbedpane, BorderLayout.CENTER);
	}//end constructor
	
	public void printSteps(int panel_num, List< boolean[]> chunks) {
		/*
		 * The method is called from core.
		 * When the core finishes its calculation,
		 * it pass all the boards to the GUI by a list of boolean array.
		 */
		
		JPanel panel;
		switch(panel_num) {
		/*
		 * For first case, the board show on the tab "Form".
		 * In the other case, it show on the tab "Essential".
		 */
			case RecordBoard.FORM_PANE:
				panel = this.contentpane;
				break;
			case RecordBoard.ESSENTIAL_PANE:
				panel = this.contentpane2;
				break;
			default:
				return;
		}//end condi.
		
		// Store each board in their own ResultPane and add it to the panel.
		for(int i = 0 ; i < chunks.size() ; i++) {
			panel.add(new ResultPane(chunks.get(i), i+1));
		}//end loop
		
		// Refresh the board to show the newest board.
		this.getContentPane().validate();
		this.getContentPane().repaint();
	}//end printStep
	
	private class ResultPane extends JPanel {
		/*
		 * This Pane Store a single board result.
		 */
		
		public ResultPane(boolean[] queenLocMap, int paneID) {
			/*
			 * This Pane is initialized by a given queen location map.
			 */
			
			// Attributes settings.
			this.setLayout(new BorderLayout());
			this.setBorder(BorderFactory.createLineBorder(Color.BLACK, 2));
			
			// Build the outer pane.
			JPanel pane = new JPanel();
			pane.add(new JLabel("Solution: " + paneID ));
			this.add(pane, BorderLayout.NORTH);
			
			// build the inner pane and set it with grid bag layout.
			pane = new JPanel();
			pane.setBorder(BorderFactory.createLineBorder(Color.BLACK, 2));
			pane.setLayout(new GridBagLayout());
			GridBagConstraints c = new GridBagConstraints();
	        c.gridwidth = 1;
	        c.gridheight = 1;
	        c.weightx = 0;
	        c.weighty = 0;
	        c.fill = GridBagConstraints.BOTH;
	        c.anchor = GridBagConstraints.WEST;
	        
	        // Give the grids blue and white and a queen if it should be.
			boolean deep_color;
			for(int i = 0 ; i < 64 ; i++) {
				c.gridx = (i%8);
		        c.gridy = i/8;
		        deep_color = ( (i%8 + i/8)%2 == 1 );
				Square square = new Square(queenLocMap[i], deep_color);
				pane.add(square, c);
			}//end loop
			this.add(pane, BorderLayout.CENTER);
		}
		
		private class Square extends JLabel {
			private final int size = 70;
			
			public Square(boolean hasIcon, boolean black) {
				/*
				 * If "hasIcon" is true, this grid gets a queen image on the board.
				 */
				this.setOpaque(true);
				this.setPreferredSize(new Dimension(size, size));
				this.setHorizontalAlignment(JLabel.CENTER);
				if(black) {
					this.setBackground(new Color(199, 238, 255));
				} else {
					this.setBackground(Color.WHITE);
				}//end condi.
				if(hasIcon) {
					this.setIcon(piece_icon);
				}//end condi.
			}
		}//end inner class Square
	}//end inner class ResultPane
}//end class
