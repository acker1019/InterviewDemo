import java.awt.Color;
import java.awt.Font;

import javax.swing.JFrame;
import javax.swing.JLayeredPane;

public class Demo extends JFrame {
	public JLayeredPane cpanel;

	private AudioPlayer song1;
	private AudioPlayer song2;
	private AudioPlayer sound_street;
	private AudioPlayer sound_sea;
	private AudioPlayer sound_forest;
	private AudioPlayer sound_boom;

	private Item background;
	private Item cena;
	private Role man;
	private Item gift;
	private Item boom;
	private Item ImgGG;
	private MessageBox msg_trans;
	private MessageBox msg;
	private MessageBox msg2;
	private MessageBox playagain;
	private MessageBox quitgame;

	private boolean keepPlay;


	//正規的方式應該是要用 JPanel/JLayeredPane 的 paintComponent(Graphics g) 這個函式來做繪圖
	//但是這邊提供一種非正規卻簡便的方式作為範例
	//若要做更進階複雜的圖形處理，還是得回到正規的方式，功能較多/執行效能也比較好
	public Demo() {
		super("Demo");
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setResizable(false);
		setSize(800, 400);
		setLocation(50, 50);
		cpanel = new JLayeredPane();
		setContentPane(cpanel);
		keepPlay = true;

		loading();
	}//cons.

	private void clearGamePane() {
		cpanel.removeAll();
		refresh();
	}//init

	private void clearSounds() {
		song1.stop();
		song2.stop();
		sound_street.stop();
		sound_sea.stop();
		sound_forest.stop();
		sound_boom.stop();
	}//closeSounds

	private void loading() {
		song1 = new AudioPlayer("/sounds/redbean.wav");
		song1.setVolume(0.95f);
		song2 = new AudioPlayer("/sounds/johncena.wav");
		sound_street = new AudioPlayer("/sounds/street.wav");
		sound_street.setVolume(0.9f);
		sound_sea = new AudioPlayer("/sounds/seawave.wav");
		sound_sea.setVolume(1.0f);
		sound_forest = new AudioPlayer("/sounds/forest.wav");
		sound_boom = new AudioPlayer("/sounds/boom.wav");
		background = new Item("background", 2);
		man = new Role("man", 6, this);
		gift = new Item("gift", 1);
		cena = new Item("johncena", 3);
		boom = new Item("boom", 1);
		ImgGG = new Item("gg", 1);
		//背景全透明
		msg_trans = new MessageBox("A Gift for You !", true, "新細明體", Font.BOLD, 36, Color.decode("#6900ff"), null);
		//背景半透明
		msg = new MessageBox("!! This is John Cena !!", false, "新細明體", Font.PLAIN, 36, Color.DARK_GRAY, new Color(20, 180, 255, 160));
		//背景不透明
		msg2 = new MessageBox("!! This is John Cena !!",
				false, "新細明體", Font.PLAIN, 36, Color.DARK_GRAY, new Color(20, 180, 255));
		playagain = new MessageBox("<html>好好玩 →<br/>我要再玩一次~</html>",
				false, "微軟正黑體", Font.PLAIN, 30, Color.DARK_GRAY, new Color(20, 200, 255, 180));
		quitgame = new MessageBox("<html>← 糞game<br/>我要走了!!</html>",
				false, "新細明體", Font.ITALIC, 20, Color.DARK_GRAY, new Color(20, 200, 255, 180));
	}//loading
	
	public boolean script() {
		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		background.setPos(0, 0);
		background.setState(0);
		man.setPos(10, 150);
		gift.setPos(600, 230);
		msg_trans.setPos(500, -120);
		//放置物件
		addObj(background, 0);
		addObj(man, 1);
		addObj(gift, 1);
		addObj(msg_trans, 2);
		refresh();

		//總是使用replay而不是play，確保第二run不會沒有聲音或是從奇怪的地方開始
		song1.replay();
		sound_street.loop();
		sound_sea.loop();

		//禮物閃爍效果
		for(int i = 0 ; i < 3 ; i++) {
			try {Thread.sleep(200);} catch (InterruptedException e) {}
			gift.setVisible(false);
			try {Thread.sleep(200);} catch (InterruptedException e) {}
			gift.setVisible(true);
		}//for

		//移動物件
		msg_trans.move(-12, 9, 500);

		//控制角色找禮物
		//在碰撞事件發生之前會停留在這一行
		man.listenCmd(2, 10);
		//碰到禮物之後

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		boom.setPos(470, 160);
		//放置物件
		cpanel.remove(man);
		cpanel.remove(gift);
		cpanel.remove(msg_trans);
		addObj(boom, 1);
		refresh();

		song1.stop();
		sound_boom.replay();

		//沒學過多執行緒，無法一次控制多個物件怎麼辦？
		//切成好幾份，輪流控至就好啦
		//反正電腦速度快到玩家看不出來
		for(int i = 0 ; i < 10 ; i++) {
			boom.vibrate(1);
			background.vibrate(1);
		}//for

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		cena.setPos(500, 170);
		cena.setState(1);
		//放置物件
		cpanel.remove(boom);
		addObj(cena, 1);
		refresh();

		song2.playFrom(20.7);

		cena.vibrate(50);

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		msg.setPos(150, 70);
		msg2.setPos(180, 120);
		//放置物件
		addObj(msg, 2);
		addObj(msg2, 2);
		refresh();

		cena.setState(2);

		//用輪流的技巧
		//一次閃爍兩個物件
		for(int i = 0 ; i < 3 ; i++) {
			try {Thread.sleep(230);} catch (InterruptedException e) {}
			msg.setVisible(false);
			msg2.setVisible(true);
			try {Thread.sleep(230);} catch (InterruptedException e) {}
			msg.setVisible(true);
			msg2.setVisible(false);
		}//for
		msg2.setVisible(true);

		//cena跳10次
		//但是切成兩半
		//因為中間想換場景
		for(int i = 0 ; i < 5 ; i++) {
			cena.move(0, -3, 10);
			cena.move(0, 3, 10);
			try {Thread.sleep(550);} catch (InterruptedException e) {}
		}//for

		//跳一半換場景
		sound_street.stop();
		sound_sea.stop();
		sound_forest.loop();
		background.setState(1);

		//cena跳
		for(int i = 0 ; i < 5 ; i++) {
			cena.move(0, -3, 10);
			cena.move(0, 3, 10);
			try {Thread.sleep(550);} catch (InterruptedException e) {}
		}//for

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		boom.setPos(470, 160);
		//放置物件
		cpanel.remove(cena);
		cpanel.remove(msg);
		cpanel.remove(msg2);
		addObj(boom, 1);
		refresh();

		song2.stop();
		sound_boom.replay();

		//一次震動兩個物件
		for(int i = 0 ; i < 15 ; i++) {
			boom.vibrate(1);
			background.vibrate(1);
		}//for

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		ImgGG.setPos(160, -130);
		//放置物件
		cpanel.remove(boom);
		addObj(ImgGG, 2);
		refresh();

		ImgGG.move(0, 3, 150);

		try {Thread.sleep(500);} catch (InterruptedException e) {}

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		man.setPos(500, 150);
		//放置物件
		addObj(man, 1);
		refresh();

		//人物閃爍
		for(int i = 0 ; i < 3 ; i++) {
			try {Thread.sleep(300);} catch (InterruptedException e) {}
			man.setVisible(false);
			try {Thread.sleep(300);} catch (InterruptedException e) {}
			man.setVisible(true);
		}//for

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		cena.setState(0);
		cena.setPos(60, 310);
		//放置物件
		addObj(cena, 3);
		refresh();

		cena.move(0, -9, 150);
		ImgGG.move(0, -12, 300);

		//總是先設定位置，才讓物件出現
		//你的畫面才不會有奇怪的殘影
		//設定位置
		playagain.setPos(-200, 50);
		quitgame.setPos(0, 95);
		//放置物件
		addObj(playagain, 4);
		addObj(quitgame, 4);
		cpanel.remove(ImgGG);
		refresh();

		playagain.move(12, 0, 800);

		//控制角色在最後的場景移動
		man.listenCmd(2, 10);

		return keepPlay;
	}//script

	public boolean checkCollision() {
		//角色拿到禮物
		if(gift.getParent() == cpanel && man.getParent() == cpanel &&
				gift.getX()-40 < man.getObjX() && man.getObjX() < gift.getX()+40) {
			return false;
		}//if
		//角色想再玩一次
		if(playagain.getParent() == cpanel && man.getParent() == cpanel &&
				playagain.getX()-40 < man.getObjX() && man.getObjX() < playagain.getX()+40) {
			keepPlay = true;
			return false;
		}//if
		//角色想離開
		if(quitgame.getParent() == cpanel && man.getParent() == cpanel &&
				quitgame.getX()-40 < man.getObjX() && man.getObjX() < quitgame.getX()+40) {
			keepPlay = false;
			return false;
		}//if
		return true;
	}//checkCollision

	private void addObj(GameObject obj, int layer) {
		cpanel.add(obj);
		cpanel.setLayer(obj, layer);
	}//addObj

	//need to refresh panel after add or remove
	private void refresh() {
		cpanel.revalidate();
		cpanel.repaint();
	}//refresh

	public static void main(String[] args) {
		//JFrame
		Demo demo = new Demo();
		demo.setVisible(true);
		boolean replay = true;
		while(replay) {
			replay = demo.script();
			demo.clearGamePane();
			demo.clearSounds();
		}//while
		System.exit(0);
	}//main
}//Demo
