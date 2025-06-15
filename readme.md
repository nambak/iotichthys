![ProjectLogo](https://raw.githubusercontent.com/nambak/iotichthys/main/public/iotichthys_logo.png)

# Iotichthys (이오티크티스)

[![Build](https://github.com/nambak/iotichthys/actions/workflows/build.yml/badge.svg?branch=main)](https://github.com/nambak/iotichthys/actions/workflows/build.yml)
![CodeRabbit Pull Request Reviews](https://img.shields.io/coderabbit/prs/github/nambak/iotichthys?utm_source=oss&utm_medium=github&utm_campaign=nambak%2Fiotichthys&labelColor=171717&color=FF570A&link=https%3A%2F%2Fcoderabbit.ai&label=CodeRabbit+Reviews)
![Coverage](https://raw.githubusercontent.com/nambak/iotichthys/image-data/coverage.svg)


> Laravel과 AWS IoT Core MQTT 서비스를 통합한 IoT 클라우드 서비스 플랫폼

## 📋 프로젝트 개요

Iotichthys는 Laravel 프레임워크와 AWS IoT Core의 MQTT 서비스를 연동하여 IoT 디바이스에서 데이터를 수집하고, 실시간 알림 및 시계열 차트와 같은 분석 도구를 제공하는 클라우드 서비스입니다.

"Iotichthys"라는 이름은 유타주의 고유종인 작은 민물고기 'Least Chub'의 학명 'Iotichthys phlegethontis'에서 따왔습니다. 작지만 중요한 생태계의 일부인 이 물고기처럼, 작은 IoT 센서들이 모여 큰 가치를 창출하는 생태계를 구축한다는 의미를 담고 있습니다.

## 🎨 시각적 아이덴티티

Iotichthys 프로젝트의 시각적 아이덴티티는 이름의 유래와 서비스의 본질을 반영합니다:

- **주 색상**: 딥 블루 (#003B73) - 데이터의 깊이와 신뢰성 표현
- **보조 색상**: 터콰이즈 (#00BFFF) - 유연성과 연결성 강조
- **액센트 색상**: 주황색 (#FF6B00) - 데이터 통찰력과 활력 표현

## 🏗️ 시스템 아키텍처

### 핵심 구성 요소

#### 1. IoT 디바이스 레이어
- 센서를 통한 데이터 수집 및 MQTT 프로토콜을 통한 데이터 전송
- AWS IoT SDK 통합, 인증서 기반 보안 연결을 갖춘 디바이스 펌웨어
- 다양한 디바이스 유형 지원: 온도/습도 센서, 에너지 미터, 위치 추적기 등

#### 2. 클라우드 인프라 레이어
- AWS IoT Core: MQTT 브로커, 디바이스 레지스트리, 메시지 라우팅
- AWS Lambda: 이벤트 기반 데이터 처리 및 변환
- Amazon DynamoDB/TimeStream: 디바이스 메타데이터 및 시계열 데이터 저장
- Amazon S3: 대용량 데이터 및 아카이브 스토리지
- Amazon SNS/SQS: 알림 및 메시지 큐

#### 3. 애플리케이션 레이어
- Laravel 백엔드: API 서비스, 비즈니스 로직, 사용자 관리
- RESTful API: 디바이스 관리, 데이터 쿼리, 알림 설정
- WebSocket 서버: 실시간 데이터 스트리밍
- 대시보드 프론트엔드: Laravel Livewire

#### 4. 분석 및 시각화 레이어
- 데이터 처리 파이프라인: 원시 데이터 정제 및 집계
- 실시간 분석 엔진: 스트림 처리 및 이상 감지
- 시각화 컴포넌트: 시계열 차트, 히트맵, 대시보드

#### 5. 알림 및 모니터링 레이어
- 알림 시스템: 임계값 기반 알림, 스케줄링된 보고서
- 모니터링 도구: 시스템 상태 및 성능 모니터링
- 멀티 채널 알림: 이메일, SMS, 푸시 알림, 인앱 알림

## 🚀 주요 기능

- **디바이스 관리**: IoT 디바이스 등록, 구성 및 모니터링
- **데이터 수집**: 다수의 디바이스로부터 안전하고 효율적인 데이터 수집
- **실시간 분석**: 데이터 스트림을 실시간으로 처리 및 분석
- **인터랙티브 대시보드**: 맞춤형 차트와 위젯을 통한 IoT 데이터 시각화
- **알림 시스템**: 규칙을 구성하고 다양한 채널을 통해 알림 수신
- **멀티테넌시**: 자원 공유 기능을 갖춘 조직 및 팀 관리
- **사용자 인증**: 안전한 API 접근 및 역할 기반 권한

## 💻 기술 스택

### 백엔드
- **프레임워크**: Laravel
- **데이터베이스**: MySQL (애플리케이션 데이터용), AWS TimeStream (시계열 데이터용)
- **클라우드 서비스**: AWS IoT Core, Lambda, DynamoDB, S3, SNS/SQS
- **인증**: Laravel Sanctum/Passport

### 프론트엔드
- **프레임워크**: Laravel Livewire
- **데이터 시각화**: Chart.js, D3.js
- **실시간 업데이트**: WebSockets (Laravel WebSockets 또는 Pusher)

## 🛠️ 설치 및 설정

### 사전 요구사항
- PHP 8.1+
- Composer
- Node.js 및 npm
- 적절한 권한을 가진 AWS 계정
- MySQL 데이터베이스

### 설정 단계
1. 저장소 복제
```bash
git clone https://github.com/yourusername/iotichthys.git
cd iotichthys
```

2. PHP 의존성 설치
```bash
composer install
```

3. JavaScript 의존성 설치
```bash
npm install
```

4. 환경 변수 설정
```bash
cp .env.example .env
# AWS 자격 증명 및 데이터베이스 설정으로 .env 파일 편집
```

5. 데이터베이스 마이그레이션 실행
```bash
php artisan migrate
```

6. 개발 서버 시작
```bash
php artisan serve
```

7. 프론트엔드 에셋 컴파일
```bash
npm run dev
```

## 🔐 AWS IoT Core 구성

1. 사물 유형 생성
```bash
aws iot create-thing-type --thing-type-name IotichthysDevice --thing-type-properties "thingTypeDescription=Iotichthys 플랫폼용 IoT 디바이스"
```

2. IoT 정책 생성
```bash
aws iot create-policy --policy-name IoTDevicePolicy --policy-document file://iot-policy.json
```

3. 디바이스 등록
```bash
# API를 사용하거나 다음 CLI 명령어 사용:
aws iot create-thing --thing-name device_id --thing-type-name IotichthysDevice
```

## 📊 데이터 흐름

1. IoT 디바이스가 센서 데이터를 수집하고 MQTT를 사용하여 AWS IoT Core에 연결
2. `devices/{device_id}/data` 패턴을 따라 특정 주제에 데이터 게시
3. AWS IoT 규칙이 메시지를 캡처하고 Lambda 함수 트리거
4. Lambda 함수가 데이터를 처리/검증하고 적절한 데이터베이스에 저장
5. Laravel 애플리케이션이 리포지토리를 통해 데이터를 검색하고 API를 통해 제공
6. 프론트엔드 대시보드가 데이터를 시각화하고 인터랙티브 컨트롤 제공
7. 알림 시스템이 수신 데이터를 모니터링하고 임계값 초과 시 알림 트리거

## 👥 기여하기

Iotichthys에 대한 기여를 환영합니다! 다음 단계를 따라주세요:

1. 저장소 포크
2. 기능 브랜치 생성 (`git checkout -b feature/amazing-feature`)
3. 변경 사항 커밋 (`git commit -m '멋진 기능 추가'`)
4. 브랜치에 푸시 (`git push origin feature/amazing-feature`)
5. Pull Request 열기

코드가 코딩 표준을 따르고 적절한 테스트가 포함되어 있는지 확인해 주세요.

## 📜 라이선스

이 프로젝트는 MIT 라이선스 하에 제공됩니다 - 자세한 내용은 LICENSE 파일을 참조하세요.

---

IoT 애호가와 개발자를 위해 ❤️를 담아 제작
