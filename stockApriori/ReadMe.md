# Association Rules of Stock Rise And Fall.

## Problem Description

For a given set of historical stock prices series,
figure out the rise-and-fall association between each price series.  
  
For example, for a given set of series {A, B, C},
one of the potential association rule could be:  
`If A and B rise, C will fall after 2 temporal units.`


## Example Dataset Description

- The dataset is constituted by 31 historical stock prices which are download from TWSE (Taiwan Stock Exchange).
- As the input of this algorithm, it was discretized by SAX technique (Symbolic Aggregate approXimation).
- Example dataset: https://drive.google.com/file/d/1WrBBrx9e7lMJkBm6fV-ZujO_lKQNcSzo/view?usp=sharing


## Data Flow

1. For N historical stock prices series.
2. Discretized N series by SAX.
3. Transform the concept of "Value Series" into "Rise/Fall Event Series".
4. Scan the Series by a sliding window to extract event frames.
5. Execute the Apriori with the event frames to obtain association rules.


## Usage

1. Download stock price history from TWSE
2. Discretize the historical prices series by SAX.
3. Put the SAXed series in "/data/StocksComSax/*.csv" as the input of "AprioriDemo.py"
4. The result are stored in "/output/result.txt" when the calculation completed.


## Result and Explanation

in "example-output/31stocks-result.txt":

```
inputed parameters:
minSup: 0.03	minConf: 0.6	wWin: 155	maxGap: 5

...
```

The above shows the configuration that:
- minimum support: 0.03
- minimum confidence: 0.6
- sliding window size: 155
- gap between each window: 5

The association rules are sorted by lift in descending order below:
```
...

Association Rules:
[support,	confidence,	lift,		rule
[0.033,		1.0,		30.011,		{IF [1326台化.t2.up.1, 1101台泥.t3.up.1] THEN [1101台泥.t4.down.1, 2382廣達.t5.up.1]} ]
[0.033,		1.0,		30.011,		{IF [1402遠東新.t2.up.1, 2912統一超.t3.up.1] THEN [1216統一.t5.up.1, 1301台塑.t5.down.1]} ]
[0.033,		1.0,		30.011,		{IF [1402遠東新.t2.up.1, 2912統一超.t3.up.1] THEN [1216統一.t5.up.1, 1402遠東新.t5.down.1]} ]
[0.033,		1.0,		30.011,		{IF [1402遠東新.t2.up.1, 2912統一超.t3.up.1] THEN [1216統一.t5.up.1, 1301台塑.t5.down.1, 1402遠東新.t5.down.1]} ]
[0.033,		0.75,		22.508,		{IF [1326台化.t2.up.1, 2308台達電.t2.up.1] THEN [2357華碩.t2.down.1, 1303南亞.t4.up.1]} ]
...
```

For the first rule:  
`{IF [1326台化.t2.up.1, 1101台泥.t3.up.1] THEN [1101台泥.t4.down.1, 2382廣達.t5.up.1]}`
,

it means:  
```
If 1326台化 rises 1 price unit at t2 and 1101台泥 rises also 1 price unit at t3,
1101台泥 will fall 1 unit at t4 and 2382廣達 will rise 1 unit at t5.
```

,and the credibility of this rule:
- support: 0.033
- confidence: 1.0
- lift: 30.011
