# Association Rules of Stock Rise And Fall.

## Problem Description

For a given set of historical stock prices series,
figure out the rise-and-fall association between each price series.  
  
For example, for a given set of series {A, B, C},
one of the result format could be:  
`If A and B rise, C will fall after 2 temporal units.`

## Example Dataset Description

- The dataset is constituted by 31 historical stock prices which are download from TWSE (Taiwan Stock Exchange).
- As the input of this algorithm, it was discretized by SAX technique (Symbolic Aggregate approXimation).
- Example dataset: https://drive.google.com/file/d/1WrBBrx9e7lMJkBm6fV-ZujO_lKQNcSzo/view?usp=sharing


## Data Flow and Description

1. For N historical stock prices series
2. Discretized N series by SAX.
3. Transform the concept of "Series" into "Rise/Fall Event"  
    sdfsdf
    sdfsdf
    sdfsdf
## Usage

1. Download stock price history from TWSE
2. Discretize the historical prices series by SAX.
3. Put the SAXed series in "/data/StocksComSax/*.csv" as the input of "AprioriDemo.py"
4. The result are stored in "/output/result.txt" when the calculation completed.


## Result and Explanation

s

