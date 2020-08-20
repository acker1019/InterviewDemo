# Author: Ackerley Cheng
# file encoding: utf-8

class LargeItemTable:

    # members

    level = None
    size = None
    eventRows = None
    countList = None

    # functions

    def __init__(self, level):
        self.level = level
        self.size = 0
        self.eventRows = []
        self.countList = []

    def add(self, eventRow, count):
        self.eventRows.append(eventRow)
        self.countList.append(count)
        self.size += 1

    def remove(self, idx):
        self.size -= 1
        del self.eventRows[idx]
        del self.countList[idx]

    def reduce(self, scalarMinSup):
        it = 0
        while(it < self.size):
            if self.countList[it] <= scalarMinSup:
                self.remove(it)
            else:
                it += 1

    # Override: len
    def __len__(self):
        return self.size

    # Override: toString
    def __repr__(self):
        out = '{'
        for idx in range(self.size):
            out += '[' + str(self.countList[idx]) + ', ' + \
                str(self.eventRows[idx]) + ']\n'
        out += '}(level=' + str(self.level) + ', size=' + str(self.size) + ')'
        return out
