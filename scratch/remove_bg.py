from PIL import Image

def remove_white_background(input_path, output_path):
    img = Image.open(input_path).convert("RGBA")
    datas = img.getdata()

    new_data = []
    for item in datas:
        # If the pixel is very close to white, make it transparent
        # R > 200, G > 200, B > 200 (aggressive threshold)
        if item[0] > 200 and item[1] > 200 and item[2] > 200:
            new_data.append((255, 255, 255, 0))
        else:
            new_data.append(item)

    img.putdata(new_data)
    img.save(output_path, "PNG")

remove_white_background("public/images/logo.png", "public/images/logo.png")
print("Aggressive background removal complete.")
