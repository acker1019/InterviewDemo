
import java.util.ArrayList;

public class Step {
	/*
	 * The data structure to store each step of queen distribution
	 * in dynamic-processing progress.
	 * This structure is calculation-oriented which means
	 * it is handy for calculation but not for space efficiency.
	 * 
	 * Grid Status:
	 * - Queen: the grid has exactly a queen.
	 * - Occupied: the gird has no queen but is in the attack range of at least one queen.
	 * - Reminder: the grid has no queen and is not occupied by any queen.
	 */
	
	//public
	public int depth;				// Queen number on the board.
	
	//private
	private boolean[] checkboard;	// true for queen; false for no queen.
	private boolean[] enable;		// true for occupied; false for unoccupied.
	
	public Step() {
		/*
		 * Initialize a empty data structure
		 */
		
		depth = 0;
		checkboard = new boolean[64];
		for(int i = 0 ; i < 64 ; i++) {
			checkboard[i] = false;
		}//end loop
		enable = new boolean[64];
		for(int i = 0 ; i < 64 ; i++) {
			enable[i] = true;
		}//end loop
	}
	
	public Step(int depth, boolean[] checkboard, boolean[] enable) {
		// Pass and store data in this structure.
		
		this.depth = depth;
		this.checkboard = new boolean[64];
		for(int i = 0 ; i < 64 ; i++) {
			this.checkboard[i] = checkboard[i];
		}//end loop
		this.enable = new boolean[64];
		for(int i = 0 ; i < 64 ; i++) {
			this.enable[i] = enable[i];
		}//end loop
	}
	
	public boolean[] getQueenLocMap() {
		/*
		 * Map this structure to a boolean array.
		 */
		
		boolean[] queenLocMap = new boolean[64];
		for(int i = 0 ; i < 64 ; i++) {
			queenLocMap[i] = this.checkboard[i];
		}//end loop
		
		return queenLocMap;
	}//end getQueenLoc
	
	public ArrayList<Integer> dump() {
		/*
		 * Inspect the location of queen in debuggin stage.
		 */
		
		ArrayList<Integer> queenLoc = new ArrayList<Integer>();
		for(int i = 0 ; i < 64 ; i++) {
			if(checkboard[i] == true) {
				queenLoc.add(i);
			}//end condi.
		}//end loop
		return queenLoc;
	}//end dump
	
	public Step clone() {
		Step cloneBoard = new Step(this.depth, this.checkboard, this.enable);
		return cloneBoard;
	}//end clone
	
	public void setPiece(int loc) {
		this.depth++;
		checkboard[loc] = true;
		occupy(loc);
	}//end setPiece
	
	public int popReminder() {
		int reminder = -1;
		for(int i = 0 ; i < 64 ; i++) {
			if(enable[i] == true) {
				enable[i] = false;
				reminder = i;
				break;
			}//end condi.
		}//end loop
		return reminder;
	}//end getReminder
	
	public int getReminderSize() {
		int size = 0;
		for(int i = 0 ; i < 64 ; i++) {
			if(enable[i] == true) {
				size++;
			}//end condi.
		}//end loop
		return size;
	}//end getReminderSize
	
	private void occupy(int loc) {
		/*
		 * For a given queen location,
		 * tag all the occupied gird by it. 
		 */
		
		//self
		enable[loc] = false;
		//up
		for(int i = loc-8 ; i > 0 ; i=i-8) {
			enable[i] = false;
		} 
		//down
		for(int i = loc+8 ; i < 64 ;  i=i+8) {
			enable[i] = false;
		}
		//left
		int b = loc - loc%8;
		int up_w = loc-9;
		int down_w = loc+7;
		for(int i = loc-1 ; i >= b ; i--) {
			enable[i] = false;
			//left-up
			if(up_w > 0) {
				enable[up_w] = false;
			}
			//left-down
			if(down_w < 64) {
				enable[down_w] = false;
			}
			up_w = up_w-9;
			down_w= down_w+7;
		}
		//right
		b = loc + (8 - loc%8);
		up_w = loc-7;
		down_w = loc+9;
		for(int i = loc+1 ; i < b ; i++) {
			enable[i] = false;
			//right-up
			if(up_w > 0) {
				enable[up_w] = false;
			}
			//right-down
			if(down_w < 64) {
				enable[down_w] = false;
			}
			up_w = up_w-7;
			down_w= down_w+9;
		}
	}//end findAtkRng
}//end class
