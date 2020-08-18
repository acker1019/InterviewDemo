# Author: Ackerley Cheng
# file encoding: utf-8


class Event:

    # members

    stockName = None
    timePoint = None
    direction = None
    extent = None

    # cons.
    def __init__(self,
                 stockName=None,
                 timePoint=None,
                 direction=None,
                 extent=None):
        self.stockName = stockName
        self.timePoint = timePoint
        self.direction = direction
        self.extent = extent

    def samePoint(self, other):
        if self.stockName == other.stockName \
                and self.timePoint == other.timePoint:
            return True
        return False

    def __key(self):
        return (self.stockName, self.timePoint, self.direction, self.extent)

    # Override: operator '=='
    def __eq__(self, other):
        if self.stockName != other.stockName:
            return False
        if self.timePoint != other.timePoint:
            return False
        if self.direction != other.direction:
            return False
        if self.extent != other.extent:
            return False
        return True

    # Override: operator '<'
    def __lt__(self, other):
        if self.timePoint < other.timePoint:
            return True
        elif self.timePoint > other.timePoint:
            return False
        if self.stockName < other.stockName:
            return True
        elif self.stockName > other.stockName:
            return False
        if self.extent < other.extent:
            return True
        return False

    # make this object hashable
    # Override: hash
    def __hash__(self):
        return hash(self.__key())

    # Override: toString
    def __repr__(self):
        out = self.stockName + '.' + 't' + str(self.timePoint) + \
            '.' + self.direction + '.' + str(abs(self.extent))
        return out
