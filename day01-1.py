input = [199, 200, 208, 210, 200, 207, 240, 269, 260, 263]

result = 0
with open('day01.txt') as file:
    prev = int(file.readline())
    while (curr := file.readline()):
        curr = int(curr)
        result += curr > prev
        prev = curr

print(result)
