
with open('day03.txt') as file:
    result = 12 * [0]
    for line in file:
        result = [ r + int(l) for r, l in zip(result, line)]
        
    gamma = ''.join(['1' if x > 500 else '0' for x in result])
    gamma = int(gamma, 2)
    print('gamma', gamma, format(gamma, 'b'))

    epsilon = ''.join(['1' if x < 500 else '0' for x in result])
    epsilon = int(epsilon, 2)
    print('epsilon', epsilon, format(epsilon, 'b'))

    print(f'power is {epsilon*gamma}')