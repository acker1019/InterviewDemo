package src;

public class Main {
	public static void main(String[] args) {
		/*
		 * This program is designed under Core-GUI separated pattern. 
		 */
		
		// Instantiate a GUI object.
		RecordBoard recordBoard = new RecordBoard();
		recordBoard.setVisible(true);
		
		// Instantiate a Core object.
		ChessPlayer player = new ChessPlayer(recordBoard);
		player.execute();
		
	}//end main
}//end class Main
