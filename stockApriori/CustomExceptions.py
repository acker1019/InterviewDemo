
class ParameterIllegalException(Exception):

    def __init__(self, msg):
        super().__init__(msg)


class AlgorithmInitializationFailedException(Exception):

    def __init__(self):
        msg = 'This Algorithm failed on initialization.' + \
              'Please check the configuration of it.'
        super().__init__(msg)
