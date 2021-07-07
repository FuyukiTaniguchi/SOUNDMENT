const duplicate = () => {
    let randoms = [];
    const min = 1,
        max = 9;
    const intRandom = (min, max) => {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    for (i = min; i <= max; i++) {
        while (true) {

            let tmp = intRandom(min, max);
            if (!randoms.includes(tmp)) {
                randoms.push(tmp);
                break;
            }
        }
    }


    return randoms.join();
}