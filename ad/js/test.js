
$.ajax('../php/test.php', {
    method: 'GET',
    data: {
        title: 'avatar',
        year: '2022',
        type: "movie"
    },
}).done(function (data) {
    console.log(data);
});

