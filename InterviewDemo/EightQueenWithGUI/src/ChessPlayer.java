package src;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.BitSet;
import java.util.HashSet;
import java.util.List;
import java.util.Stack;

import javax.swing.SwingWorker;

public class ChessPlayer extends SwingWorker<Void, boolean[]> {
	/*
	 * The core work as a swing worker to keep the GUI responding.
	 */
	
	// gameStack for backtracking calculation. 
	private Stack<Step> gameStack;
	
	// solutions keeps all the valid solutions.
	private ArrayList<boolean[]> solutions;
	
	// recordBoard is the handler to the GUI given in the constructing stage.
	private RecordBoard recordBoard;
	
	private int setPiece_times;
	private long init_time;
	private long fin_time ;
	private long cal_during;
	
	public ChessPlayer(RecordBoard recordBoard) {
		this.recordBoard = recordBoard;
		this.gameStack= new Stack<Step>();
		this.solutions= new ArrayList<boolean[]>();
	}
	
	@Override
	protected Void doInBackground() throws Exception {
		/*
		 * The task the swing worker takes to find the solutions
		 * by backtracking dynamic processing.
		 */
		
		//init
		Step curStep = new Step();
		gameStack.push(curStep);
		int curPieceLoc;
		setPiece_times = 0;
		init_time = System.currentTimeMillis();
		
		/*
		 * Grid Status:
		 * - Queen: the grid has exactly a queen.
		 * - Occupied: the gird has no queen but is in the attack range of at least one queen.
		 * - Reminder: the grid has no queen and is not occupied by any queen.
		 */
	
		// script: batracking dynamic processing to find solutions.
		while( !gameStack.empty() ) {
			if( curStep.getReminderSize() > 0 ) {
				/*
				 * If there are still reminders.
				 */
				curPieceLoc = curStep.popReminder();	// get next reminder.
				curStep = curStep.clone();				// clone current step to be the next step.
				curStep.setPiece(curPieceLoc);			// place a piece on the reminder.
				gameStack.push(curStep);				// push current step into the stack.
				recordBoard.state_bar.setText("Caculating...  (No. " + ++setPiece_times + " setting piece)");
			} else {
				/*
				 * If there are no reminders.
				 */
				if(curStep.depth >= 8) {
					/*
					 * If there are at least 8 queen on the board,
					 * store this result.
					 */
					
					boolean[] map = curStep.getQueenLocMap();
					publish(map);
					solutions.add(map);
				}//end condi.
				
				// retrieve previous step.
				gameStack.pop();
				curStep = gameStack.peek();
				
			}//end condi.
		}//end script
		
		//conclude
		this.fin_time = System.currentTimeMillis();
		this.cal_during = fin_time - init_time;
		
		return null;
	}//end doInBackground
	
	@Override
	protected void process(List< boolean[]> chunks) {
		recordBoard.printSteps( RecordBoard.FORM_PANE, chunks );
	}//process
	
	@Override
	protected void done() {
		ArrayList<boolean[]> rootSolutions = this.findRootSolution();
		System.out.println(init_time);
		System.out.println(fin_time);
		System.out.println(cal_during);
		recordBoard.printSteps( RecordBoard.ESSENTIAL_PANE, rootSolutions );
		recordBoard.state_bar.setText(
				"(done) - time usage: " + cal_during + 
				"(msec) / set piece: " + setPiece_times + 
				"(times) / valid solution: " + solutions.size() + 
				" / root solution: " + rootSolutions.size());
	}//end done
	
	private ArrayList<boolean[]> findRootSolution() {
		/*
		 * 
		 */
		
		ArrayList<boolean[]> rootSolutions = new ArrayList<boolean[]>();
		HashSet<BitSet> signaturePool = new HashSet<BitSet>();
		
		for(boolean[] curMap : solutions) {
			BitSet sig = this.getBoardSignature(curMap);
			
			if(!signaturePool.contains(sig)) {
				rootSolutions.add(curMap);
				
				// register for new signatures
				
				// the original directions
				signaturePool.add(sig);
				
				// the other 3 directions
				BitSet[] signatures = this.getSignatureInOther3Direction(curMap);
				for(BitSet signature : signatures) {
					signaturePool.add(signature);
				}
				
				// mirrored  map
				boolean[] mirroredMap = this.mirror_board(curMap);
				sig = this.getBoardSignature(mirroredMap);
				signaturePool.add(sig);
				
				// the other 3 directions of mirrored map
				signatures = this.getSignatureInOther3Direction(mirroredMap);
				for(BitSet signature : signatures) {
					signaturePool.add(signature);
				}
				
			}// if
		}//end loop curIndex
		
		return rootSolutions;
	}//end findRootSolution
	
	private BitSet getBoardSignature(boolean[] map) {
		BitSet sig = new BitSet(map.length);
		for(int i = 0 ; i < map.length ; i++) {
			if(map[i]) {
				sig.set(i);
			}
		}//end loop i
		return sig;
	}//end getBoardSignature
	
	private boolean[] rotateBoard(boolean[] map) {
		int k = 0;
		boolean[] rotatedMap = new boolean[64];
		
		for(int i = 7 ; i >= 0 ; i--) {
			for(int j = 0 ; j < 64 ; j += 8) {
				rotatedMap[k++] = map[i+j];
			}//end loop j
		}//end loop i
		
		return rotatedMap;
	}//end rotate_board
	
	private boolean[] mirror_board(boolean[] map) {
		int k = 0;
		boolean[] mirroredMap = new boolean[64];
		
		for(int i = 0 ; i < 64 ; i += 8) {
			for(int j = 7 ; j >= 0 ; j--) {
				mirroredMap[k++] = map[i+j];
			}//end loop j
		}//end loop i
		
		return mirroredMap;
	}//end mirror_board
	
	private BitSet[] getSignatureInOther3Direction(boolean[] map) {
		BitSet[] signatures = new BitSet[3];
		
		// the other 3 directions of rotation
		boolean[] rotatedMap = map;
		for(int i = 0 ; i < 3 ; i++) {
			rotatedMap = this.rotateBoard(rotatedMap);
			signatures[i] = this.getBoardSignature(rotatedMap);
		}// for loop i
		
		return signatures;
	}//end getSignatureIn4Direction

}//end class
