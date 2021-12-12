
with open('day03.txt') as file:
    lines = [[ int(_) for _ in line.rstrip()] for line in file]
    oxygen = ''
    for position in range(12):
        result = 0
        for line in lines:
            result += line[position]
        
        oxygen_bit = int(result >= len(lines)/2)    
        oxygen += str(oxygen_bit)
        # print(f'{oxygen=} {oxygen_bit=} {len(lines)=} {result=}')

        lines = [line for line in lines if line[position] == oxygen_bit]
        if len(lines) == 1:
            # print(f'breaking at {position=}')
            oxygen = lines[0]
            break
oxygen = int("".join([str(_) for _ in oxygen]),2)
print(f'final {oxygen=}')

with open('day03.txt') as file:
    lines = [[ int(_) for _ in line.rstrip()] for line in file]
    co2 = ''
    for position in range(12):
        result = 0
        for line in lines:
            result += line[position]
        
        co2_bit = int(result < len(lines)/2)    
        co2 += str(co2_bit)
        # print(f'{co2=} {co2_bit=} {len(lines)=} {result=}')

        lines = [line for line in lines if line[position] == co2_bit]
        if len(lines) == 1:
            # print(f'breaking at {position=}')
            co2 = lines[0]
            break
co2 = int("".join([str(_) for _ in co2]),2)
print(f'final {co2=}')

print(f'life support is {oxygen*co2}')

exit()
gamma = ''.join(['1' if x > 500 else '0' for x in result])
gamma = int(gamma, 2)
print('gamma', gamma, format(gamma, 'b'))

epsilon = ''.join(['1' if x < 500 else '0' for x in result])
epsilon = int(epsilon, 2)
print('epsilon', epsilon, format(epsilon, 'b'))

print(f'power is {epsilon*gamma}')