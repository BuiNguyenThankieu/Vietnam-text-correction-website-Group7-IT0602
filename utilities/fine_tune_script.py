import openai
import os

# đã đặt API key
openai.api_key = os.getenv("OPENAI_API_KEY")

# Đường dẫn đến tệp dữ liệu JSONL đã chuẩn bị
file_path = "../vietnamese_spellcheck_data_prepared.jsonl"

# Tạo file upload
response = openai.File.create(
    file=open(file_path, "rb"),
    purpose='fine-tune'
)

print("File ID:", response.id)

# Thực hiện quá trình fine-tune
fine_tune_response = openai.FineTune.create(
    training_file=response.id,
    model="curie"
)

print("Fine-tune initiated:", fine_tune_response.id)
