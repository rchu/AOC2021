
result = 0
with open('day01.txt') as file:
    numbers = [
        int(file.readline()),
        int(file.readline()),
        int(file.readline()),
    ]
    result = 0  
    while (number := file.readline().rstrip()):
        number = int(number)
        print(f"{result} {numbers} = {sum(numbers)} and new number is {number} sum = {sum(numbers[1:]) + number}")
        result += sum(numbers) < sum(numbers[1:]) + number
        numbers = [numbers[1], numbers[2], number]
print(result)
