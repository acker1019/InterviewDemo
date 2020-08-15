import javax.sound.sampled.AudioFormat;
import javax.sound.sampled.AudioInputStream;
import javax.sound.sampled.AudioSystem;
import javax.sound.sampled.Clip;
import javax.sound.sampled.DataLine;
import javax.sound.sampled.FloatControl;

public class AudioPlayer {
	private Clip audioClip;

	public AudioPlayer(String filepath) {
		try {
			// 從檔案建立音訊串流
			AudioInputStream audioStream = AudioSystem.getAudioInputStream(Demo.class.getResource(filepath));
			// 取得音訊格式
			AudioFormat format = audioStream.getFormat();
			// 建立音訊控制介面
			DataLine.Info info = new DataLine.Info(Clip.class, format);
			// 建立音訊撥放器
			audioClip = (Clip) AudioSystem.getLine(info);
			audioClip.open(audioStream);
		} catch (Exception e) {
			System.out.println("檔案不存在");
		} // try-catch
	}// AudioPlayer

	public void play() {
		audioClip.start();
	}// replay

	public void replay() {
		audioClip.setMicrosecondPosition(0);
		audioClip.start();
	}// replay

	public void playFrom(double sec) {
		audioClip.setMicrosecondPosition((int)(sec * 1000000));
		audioClip.start();
	}// playFrom

	public void stop() {
		audioClip.stop();
	}// replay

	public void loop() {
		audioClip.loop(Clip.LOOP_CONTINUOUSLY);
	}// replay

	public void loop(int count) {
		audioClip.loop(count);
	}// replay

	public void setVolume(float volume) {
		FloatControl gainControl = (FloatControl) audioClip.getControl(FloatControl.Type.MASTER_GAIN);
		float max = gainControl.getMaximum();
		float min = gainControl.getMinimum();
		float db = volume * (max - min) + min;
		gainControl.setValue(db);
	}//setVolume
}// AudioPlayer
